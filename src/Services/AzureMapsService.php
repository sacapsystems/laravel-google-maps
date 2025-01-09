<?php

namespace Sacapsystems\LaravelAzureMaps\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Exception;

class AzureMapsService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = Config::get('azure-maps.base_url');
        $this->apiKey = Config::get('azure-maps.api_key');
    }

    public function searchSchools($query, $limit = 5)
    {
        $response = Http::get($this->baseUrl, [
            'api-version' => '1.0',
            'subscription-key' => $this->apiKey,
            'query' => $query,
            'limit' => $limit,
            'categorySet' => '7372',
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch search results');
        }

        if ($response->json('summary.numResults') > 0) {
            return $this->formatResults($response->json());
        }

        return [];
    }

    public function searchAddress($query, $limit = 5)
    {
        $response = Http::get($this->baseUrl, [
            'api-version' => '1.0',
            'subscription-key' => $this->apiKey,
            'query' => $query,
            'limit' => $limit,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch search results');
        }

        if ($response->json('summary.numResults') > 0) {
            return $this->formatResults($response->json());
        }

        return [];
    }

    private function formatResults($data)
    {
        return collect($data['results'] ?? [])->map(function ($result) {
            $address = $result['address'] ?? [];

            return [
                'name' => $result['poi']['name'] ?? '',
                'address' => [
                    'line1' => trim(($address['streetNumber'] ?? '') . ' ' . ($address['streetName'] ?? '')),
                    'line2' => trim((isset($address['municipalitySubdivision']) ? $address['municipalitySubdivision'] . ', ' : '') . ($address['municipality'] ?? '')),
                    'suburb' => $address['municipalitySubdivision'] ?? null,
                    'city' => $address['municipality'] ?? null,
                    'postalCode' => $address['postalCode'] ?? null,
                    'province' => $address['countrySubdivision'] ?? null,
                    'provinceCode' => $address['countrySubdivisionCode'] ?? null,
                    'country' => $address['country'] ?? null,
                    'countryCodeISO3' => $address['countryCodeISO3'] ?? null,
                ],
                'coordinates' => [
                    'lat' => $result['position']['lat'] ?? null,
                    'lng' => $result['position']['lon'] ?? null
                ],
            ];
        })->toArray();
    }
}
