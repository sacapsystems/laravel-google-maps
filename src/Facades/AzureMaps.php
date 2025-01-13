<?php

namespace Sacapsystems\LaravelAzureMaps\Facades;

use Illuminate\Support\Facades\Facade;
use Sacapsystems\LaravelAzureMaps\Services\AzureMapsService;

class AzureMaps extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AzureMapsService::class;
    }
}
