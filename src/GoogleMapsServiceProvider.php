<?php

namespace Sacapsystems\LaravelGoogleMaps;

use Illuminate\Support\ServiceProvider;
use Sacapsystems\LaravelGoogleMaps\Services\GoogleMapsService;

class GoogleMapsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/google-maps.php',
            'google-maps'
        );

        $this->app->singleton(GoogleMapsService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/google-maps.php' => $this->app->configPath('google-maps.php'),
        ], 'config');
    }
}
