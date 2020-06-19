<?php

namespace Madnest\MadstoreGopay;

use Illuminate\Support\ServiceProvider;

class MadstoreGopayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'madstore-gopay');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'madstore-gopay');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/madstore-gopay.php' => config_path('madstore-gopay.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/madstore-gopay'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/madstore-gopay'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/madstore-gopay'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/madstore-gopay.php', 'madstore-gopay');

        // Register the main class to use with the facade
        $this->app->singleton('madstore-gopay', function () {
            return new MadstoreGopay;
        });
    }
}
