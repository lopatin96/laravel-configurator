<?php

namespace Atin\LaravelConfigurator\Models;

use Atin\LaravelConfigurator\Enums\ConfigCategory;
use Atin\LaravelConfigurator\Enums\ConfigKey;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Atin\LaravelConfigurator\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Nova\Actions\Actionable;

class Config extends Model
{
    use Actionable, HasFactory;

    protected $casts = [
        'type' => ConfigType::class,
        'category' => ConfigCategory::class,
    ];

    public static function getValue(ConfigKey $configKey): string|array|bool|int|float
    {
        $data = self::getData($configKey);

        return self::convertToValue($data['type'], $data['value']);
    }

    private static function getData(ConfigKey $configKey): array
    {
        if ($data = Cache::get('configs.'.$configKey->name)) {
            return $data;
        }
        self::updateConfig();

        return self::getData($configKey);
    }

    private static function updateConfig(): void
    {
        foreach (self::all() as $config) {
            Cache::put(
                'configs.'.$config->key,
                [
                    'type' => $config->type,
                    'value' => $config->value,
                ],
                60
            );
        }
    }

    public static function getLimitedValue(ConfigKey $configKey, User $user = null): string|array|bool|int|float
    {
        return self::getValue(self::getLimitedVersionConfigKey($configKey, $user ?? Auth::user() ?? null));
    }

    private static function getLimitedVersionConfigKey(ConfigKey $configKey, User $user = null): ConfigKey|int
    {
//        switch ($configKey) {
//            case ConfigKey::BulkImportLimit:
//            case ConfigKey::BulkImportLimitPro:
//                return $user->isPro()
//                    ? ConfigKey::BulkImportLimitPro
//                    : ConfigKey::BulkImportLimit;
//            case ConfigKey::NotificationsFeatureMailNotificationRateLimit:
//            case ConfigKey::NotificationsFeatureMailNotificationRateLimitPro:
//                return $user->isPro()
//                    ? ConfigKey::NotificationsFeatureMailNotificationRateLimitPro
//                    : ConfigKey::NotificationsFeatureMailNotificationRateLimit;
//            case ConfigKey::LinksLinksLimit:
//            case ConfigKey::LinksLinksLimitPro:
//                return $user->isPro()
//                    ? ConfigKey::LinksLinksLimitPro
//                    : ConfigKey::LinksLinksLimit;
//            case ConfigKey::ActiveLinksLimit:
//            case ConfigKey::ActiveLinksLimitPro:
//                return $user->isPro()
//                    ? ConfigKey::ActiveLinksLimitPro
//                    : ConfigKey::ActiveLinksLimit;
//            case ConfigKey::ClicksClicksLimit:
//            case ConfigKey::ClicksClicksLimitPro:
//                return $user->isPro()
//                    ? ConfigKey::ClicksClicksLimitPro
//                    : ConfigKey::ClicksClicksLimit;
//            default:
//                return $configKey;
//        }
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
