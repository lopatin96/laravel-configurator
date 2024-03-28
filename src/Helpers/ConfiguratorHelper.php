<?php

namespace Atin\LaravelConfigurator\Helpers;

use App\Enums\ConfigKey;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Atin\LaravelConfigurator\Models\Config;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ConfiguratorHelper
{
    public static function getLimitedValue(ConfigKey $configKey, User $user = null): string|array|bool|int|float
    {
        return self::getValue(self::getLimitedVersionConfigKey($configKey, $user ?? auth()->user()));
    }

    public static function getValue(ConfigKey $configKey): string|array|bool|int|float
    {
        $data = self::getData($configKey);

        return self::convertToValue($data['type'], $data['value']);
    }

    public static function getData(ConfigKey $configKey): array
    {
        if ($data = Cache::get('configs.'.$configKey->value)) {
            return $data;
        }

        self::updateCache();

        return Config::where('key', $configKey)->first()->getData();
    }

    private static function updateCache(): void
    {
        foreach (Config::all() as $config) {
            Cache::put(
                'configs.'.$config->key,
                [
                    'type' => $config->type,
                    'value' => $config->value,
                ],
                config("laravel-configurator.cache_expiration_in_seconds") ?? 60
            );
        }
    }

    private static function getLimitedVersionConfigKey(ConfigKey $configKey, User $user = null): ConfigKey|int
    {
        return $user
            && array_key_exists($configKey->value, config('laravel-subscription'))
            && array_key_exists($user->getSubscribedPlanLevel(), config("laravel-subscription.$configKey->value"))
                ? ConfigKey::from(config("laravel-subscription.$configKey->value")[$user->getSubscribedPlanLevel()])
                : $configKey;
    }

    public static function convertToValue(ConfigType $type, string $value): string|array|bool|int|float
    {
        return match ($type) {
            ConfigType::String => $value,
            ConfigType::Integer => (int) $value,
            ConfigType::Float => (float) $value,
            ConfigType::Boolean => (bool) $value,
            ConfigType::ArrayOfStrings => array_map('strval', array_map('trim', explode(',', $value))),
            ConfigType::ArrayOfIntegers => array_map('intval', array_map('trim', explode(',', $value))),
        };
    }

    public static function getString(ConfigKey $configKey, string $implodeWithSeparator = ','): string
    {
        $data = self::getData($configKey);

        return self::convertToString($data['type'], $data['value'], $implodeWithSeparator);
    }

    private static function convertToString(ConfigType $type, mixed $value, string $implodeWithSeparator): string
    {
        return match ($type) {
            ConfigType::ArrayOfStrings => implode(
                $implodeWithSeparator,
                array_map('strval', array_map('trim', explode(',', $value)))
            ),
            ConfigType::ArrayOfIntegers => implode(
                $implodeWithSeparator,
                array_map('intval', array_map('trim', explode(',', $value)))
            ),
            default => (string) $value,
        };
    }
}