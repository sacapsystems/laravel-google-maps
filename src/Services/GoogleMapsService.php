<?php

namespace Sacapsystems\LaravelGoogleMaps\Services;

use Illuminate\Support\Facades\Config;
use Sacapsystems\LaravelGoogleMaps\Builders\QueryBuilder;

class GoogleMapsService
{
    private QueryBuilder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = new QueryBuilder(
            Config::get('google-maps.base_url'),
            Config::get('google-maps.api_key')
        );
    }

    public function searchAddress(string $query): QueryBuilder
    {
        return $this->queryBuilder->newSearch($query);
    }

    public function searchHighSchools(string $query): QueryBuilder
    {
        return $this->queryBuilder->newSearch($query, 'secondary_school');
    }

    public function getPlaceDetails(string $placeId): QueryBuilder
    {
        return $this->queryBuilder->getPlaceDetails($placeId);
    }
}
