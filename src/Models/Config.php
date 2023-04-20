<?php

namespace Atin\LaravelConfigurator\Models;

use App\Enums\ConfigCategory;
use Atin\LaravelConfigurator\Enums\ConfigType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Actions\Actionable;


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
}
