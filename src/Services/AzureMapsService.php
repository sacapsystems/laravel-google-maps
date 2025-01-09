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
        return $this->search($query, $limit, '7372');
    }

    public function searchAddress($query, $limit = 5)
    {
        return $this->search($query, $limit);
    }

    private function search($query, $limit, $categorySet = null)
    {
        $params = [
            'api-version' => '1.0',
            'subscription-key' => $this->apiKey,
            'query' => $query,
            'limit' => $limit,
        ];

        if ($categorySet) {
            $params['categorySet'] = $categorySet;
        }

        $response = Http::get($this->baseUrl, $params);

        if ($response->failed()) {
            throw new Exception('Failed to fetch search results');
        }

        return $response->json('summary.numResults') > 0
        ? json_encode($this->formatResults($response->json()))
        : json_encode([]);
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
