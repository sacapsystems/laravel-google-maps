<?php

namespace Sacapsystems\LaravelAzureMaps\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Sacapsystems\LaravelAzureMaps\AzureMapsServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            AzureMapsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('azure-maps.api_key', 'test-key');
        $app['config']->set('azure-maps.base_url', 'https://atlas.microsoft.com/search/fuzzy/json');
    }
}
