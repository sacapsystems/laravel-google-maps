<?php

namespace Sacapsystems\LaravelGoogleMaps\Builders;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sacapsystems\LaravelGoogleMaps\Exceptions\GoogleMapsException;

class QueryBuilder
{
    private array $params;
    private array $baseUrl;
    private string $apiKey;
    private string $endpoint;
    private Client $client;

    public function __construct(array $baseUrl, string $apiKey, ?Client $client = null)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->client = $client ?? new Client();
    }

    public function newSearch(string $query, ?string $type = null): self
    {
        $this->endpoint = 'search';
        $this->params = [
            'key' => $this->apiKey,
            'input' => $query
        ];

        if ($type) {
            $this->params['type'] = $type;
        }

        return $this;
    }

    public function getPlaceDetails(string $placeId): self
    {
        $this->endpoint = 'details';
        $this->params = [
            'key' => $this->apiKey,
            'place_id' => $placeId,
            'fields' => 'address_component,formatted_address,geometry,name'
        ];

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->params['maxResults'] = $limit;
        return $this;
    }

    public function location(float $lat, float $lon, int $radius = 50000): self
    {
        $this->params['location'] = "{$lat},{$lon}";
        $this->params['radius'] = $radius;
        return $this;
    }
    public function get(): string
    {
        try {
            $response = $this->client->get($this->baseUrl[$this->endpoint], [
                'query' => $this->params
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['status'] !== 'OK') {
                throw new GoogleMapsException('Failed to fetch results');
            }

            return $this->endpoint === 'search'
                ? $this->formatSearchResults($data)
                : $this->formatDetailResults($data);
        } catch (GuzzleException $e) {
            throw new GoogleMapsException('Failed to fetch results: ' . $e->getMessage());
        }
    }

    private function formatSearchResults(array $response): string
    {
        $formattedResults = collect($response['predictions'] ?? [])->map(function ($result) {
            return [
                'place_id' => $result['place_id'],
                'name' => $result['description'] ?? '',
            ];
        });

        return json_encode($formattedResults);
    }

    private function formatDetailResults(array $response): string
    {
        $result = $response['result'];
        $addressComponents = collect($result['address_components'] ?? []);

        $streetNumber = $this->findAddressComponent($addressComponents, 'street_number');
        $streetName = $this->findAddressComponent($addressComponents, 'route');
        $suburb = $this->findAddressComponent($addressComponents, 'sublocality');
        $city = $this->findAddressComponent($addressComponents, 'locality');

        $line1 = trim(($streetNumber ?? '') . ' ' . ($streetName ?? ''));
        $line2 = trim(($suburb ? $suburb . ', ' : '') . ($city ?? ''));

        $formattedResult = [
            'name' => $result['name'] ?? '',
            'address' => [
                'line1' => $line1,
                'line2' => $line2,
                'street_number' => $streetNumber,
                'street_name' => $streetName,
                'suburb' => $suburb,
                'city' => $city,
                'postalCode' => $this->findAddressComponent($addressComponents, 'postal_code'),
                'province' => $this->findAddressComponent($addressComponents, 'administrative_area_level_1'),
                'provinceCode' => $this->findAddressComponent($addressComponents, 'administrative_area_level_1', true),
                'country' => $this->findAddressComponent($addressComponents, 'country'),
                'countryCode' => $this->findAddressComponent($addressComponents, 'country', true),
            ],
            'coordinates' => [
                'lat' => $result['geometry']['location']['lat'] ?? null,
                'lng' => $result['geometry']['location']['lng'] ?? null,
            ],
        ];

        return json_encode($formattedResult);
    }

    private function findAddressComponent($components, $type, $useShortName = false): ?string
    {
        $component = $components->first(function ($component) use ($type) {
            return in_array($type, $component['types'] ?? []);
        });

        return $component ? ($useShortName ? $component['short_name'] : $component['long_name']) : null;
    }
}
