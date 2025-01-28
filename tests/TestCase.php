<?php

namespace Sacapsystems\LaravelGoogleMaps\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Sacapsystems\LaravelGoogleMaps\GoogleMapsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            GoogleMapsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('google-maps.api_key', 'test-key');
        $app['config']->set('google-maps.base_url', 'https://maps.googleapis.com/maps/api');
    }
}
