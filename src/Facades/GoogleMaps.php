<?php

namespace Sacapsystems\LaravelGoogleMaps\Facades;

use Illuminate\Support\Facades\Facade;
use Sacapsystems\LaravelGoogleMaps\Services\GoogleMapsService;

class GoogleMaps extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GoogleMapsService::class;
    }
}
