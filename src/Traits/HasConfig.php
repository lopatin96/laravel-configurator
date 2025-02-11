<?php

namespace Atin\LaravelConfigurator\Traits;

use App\Enums\ConfigKey;
use Atin\LaravelConfigurator\Helpers\ConfiguratorHelper;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

trait HasConfig
{
    public function getConfig(ConfigKey $configKey): string|array|bool|int|float
    {
        if ($value = $this->config?->{$configKey->name} ?? null) {
            return ConfiguratorHelper::convertToValue(ConfiguratorHelper::getData($configKey)['type'], $value);
        }

        return ConfiguratorHelper::getLimitedValue($configKey, $this);
    }

    public function setConfigValue(ConfigKey $configKey, $newValue): bool
    {
        return $this->forceFill([
            "config->$configKey->name" => $newValue,
        ])->save();
    }

    public function incrementConfigValue(ConfigKey $configKey, int $amount = 1): bool
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount value passed to increment method must be a positive number.');
        }

        $configKeyName = $configKey->name;

        $this->setZeroValueIfMissingOrNull($configKey);

        return DB::table('users')
            ->where('id', $this->id)
            ->update([
                'config' => DB::raw('JSON_SET(
                    COALESCE(config, "{}"), 
                    "$.' . $configKeyName . '", 
                    CAST(COALESCE(JSON_EXTRACT(config, "$.' . $configKeyName . '"), 0) AS INTEGER) + ' . $amount . '
                )')
            ]);
    }

    public function decrementConfigValue(ConfigKey $configKey, int $amount = 1): bool
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount value passed to increment method must be a positive number.');
        }

        $configKeyName = $configKey->name;

        $this->setZeroValueIfMissingOrNull($configKey);

        return DB::table('users')
            ->where('id', $this->id)
            ->update([
                'config' => DB::raw('JSON_SET(
                    COALESCE(config, "{}"), 
                    "$.' . $configKeyName . '", 
                    CAST(COALESCE(JSON_EXTRACT(config, "$.' . $configKeyName . '"), 0) AS INTEGER) - ' . $amount . '
                )')
            ]);
    }

    private function setZeroValueIfMissingOrNull(ConfigKey $configKey): void
    {
        $configKeyName = $configKey->name;

        $currentValue = DB::table('users')
            ->where('id', $this->id)
            ->value('config');

        $configData = json_decode($currentValue, true);

        if (! isset($configData[$configKeyName]) || is_null($configData[$configKeyName])) {
            $this->setConfigValue($configKey, 0);
        }
    }
}
