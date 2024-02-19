<?php

namespace Atin\LaravelConfigurator;

use Illuminate\Support\ServiceProvider;

class ConfiguratorProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-configurator');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('/migrations')
        ], 'laravel-configurator-migrations');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-configurator.php')
        ], 'laravel-configurator-config');
    }
}