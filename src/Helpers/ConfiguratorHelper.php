<?php

namespace Atin\LaravelConfigurator\Helpers;

use App\Enums\ConfigKey;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Atin\LaravelConfigurator\Models\Config;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ConfiguratorHelper
{
    public static function getLimitedValue(ConfigKey $configKey, User $user = null): string|array|bool|int|float
    {
        return self::getValue(
            self::getLimitedVersionConfigKey(
                $configKey,
                $user ?? Auth::user()
            )
        );
    }

    public static function getValue(ConfigKey $configKey): string|array|bool|int|float
    {
        $data = self::getData($configKey);

        return self::convertToValue($data['type'], $data['value']);
    }

    private static function getData(ConfigKey $configKey): array
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
            && array_key_exists($user->getSubscribedPlan(), config("laravel-subscription.$configKey->value"))
                ? ConfigKey::from(config("laravel-subscription.$configKey->value")[$user?->getSubscribedPlan()])
                : $configKey;
    }

    private static function convertToValue(ConfigType $type, string $value): string|array|bool|int|float|null
    {
        switch ($type) {
            case ConfigType::String:
                return $value;
            case ConfigType::Integer:
                return (int) $value;
            case ConfigType::Float:
                return (float) $value;
            case ConfigType::Boolean:
                return (bool) $value;
            case ConfigType::ArrayOfStrings:
                return array_map('strval', array_map('trim', explode(',', $value)));
            case ConfigType::ArrayOfIntegers:
                return array_map('intval', array_map('trim', explode(',', $value)));
        }

        return null;
    }

    public static function getString(
        ConfigKey $configKey,
        string $implodeWithSeparator = ','
    ): string {
        $data = self::getData($configKey);

        return self::convertToString($data['type'], $data['value'], $implodeWithSeparator);
    }

    private static function convertToString(ConfigType $type, mixed $value, string $implodeWithSeparator): string
    {
        switch ($type) {
            case ConfigType::ArrayOfStrings:
                return implode(
                    $implodeWithSeparator,
                    array_map('strval', array_map('trim', explode(',', $value)))
                );
            case ConfigType::ArrayOfIntegers:
                return implode(
                    $implodeWithSeparator,
                    array_map('intval', array_map('trim', explode(',', $value)))
                );
            default:
                return (string) $value;
        }
    }
}