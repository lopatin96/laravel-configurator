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
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-social-auth');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravel-social-auth');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('/migrations')
        ], 'laravel-social-auth-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-social-auth')
        ], 'laravel-social-auth-views');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/laravel-social-auth'),
        ], 'laravel-social-auth-lang');
    }
}