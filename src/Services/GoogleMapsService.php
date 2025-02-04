<?php

namespace Sacapsystems\LaravelGoogleMaps\Services;

use Illuminate\Support\Facades\Config;
use Sacapsystems\LaravelGoogleMaps\Builders\QueryBuilder;

class GoogleMapsService
{
    private QueryBuilder $queryBuilder;
    private $queryBuilderFactory;

    public function __construct(?callable $queryBuilderFactory = null)
    {
        $this->queryBuilderFactory = $queryBuilderFactory ?? function () {
            return new QueryBuilder(
                Config::get('google-maps.base_url'),
                Config::get('google-maps.api_key')
            );
        };

        $this->queryBuilder = ($this->queryBuilderFactory)();
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
