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

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('/migrations')
        ], 'laravel-social-auth-migrations');
    }
}