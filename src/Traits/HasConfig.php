<?php

namespace Atin\LaravelConfigurator\Traits;

use App\Enums\ConfigKey;
use Atin\LaravelConfigurator\Helpers\ConfiguratorHelper;

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

        return $this->forceFill([
            "config->$configKey->name" => $this->getConfig($configKey) + $amount,
        ])->save();
    }

    public function decrementConfigValue(ConfigKey $configKey, int $amount = 1): bool
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount value passed to increment method must be a positive number.');
        }

        return $this->forceFill([
            "config->$configKey->name" => $this->getConfig($configKey) - $amount,
        ])->save();
    }
}
