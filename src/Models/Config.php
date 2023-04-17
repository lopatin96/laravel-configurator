<?php

namespace Atin\LaravelConfigurator\Models;

use Atin\LaravelConfigurator\Enums\ConfigCategory;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;
use Illuminate\Support\Facades\Cache;

class Config extends Model
{
    use Actionable, HasFactory;

    protected $casts = [
        'type' => ConfigType::class,
        'category' => ConfigCategory::class,
    ];

    public function getData(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::retrieved(static function (Model $model) {
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
        });
    }
}
