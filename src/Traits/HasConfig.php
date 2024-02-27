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

        return ConfiguratorHelper::getLimitedValue($configKey, auth()->user());
    }
}
