<?php

namespace Sacapsystems\LaravelAzureMaps;

use Illuminate\Support\ServiceProvider;
use Sacapsystems\LaravelAzureMaps\Services\AzureMapsService;

class AzureMapsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/azure-maps.php', 'azure-maps'
        );

        $this->app->singleton(AzureMapsService::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/azure-maps.php' => $this->app->configPath('azure-maps.php'),
        ], 'config');
    }
}
