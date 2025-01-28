<?php

namespace Sacapsystems\LaravelGoogleMaps\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Sacapsystems\LaravelGoogleMaps\Exceptions\GoogleMapsException;
use Sacapsystems\LaravelGoogleMaps\Services\GoogleMapsService;
use Sacapsystems\LaravelGoogleMaps\Tests\TestCase;

class GoogleMapsServiceTest extends TestCase
{
    protected $service;
    protected $mockAutocompleteResponse;
    protected $mockDetailsResponse;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure the service with test values
        config([
            'google-maps.api_key' => 'test-api-key',
            'google-maps.base_url' => [
                'search' => 'https://maps.googleapis.com/maps/api/place/autocomplete/json',
                'details' => 'https://maps.googleapis.com/maps/api/place/details/json'
            ]
        ]);

        $this->service = new GoogleMapsService();

        // Mock for autocomplete response
        $this->mockAutocompleteResponse = [
            'status' => 'OK',
            'predictions' => [
                [
                    'description' => '123 Main Street, Cape Town, South Africa',
                    'place_id' => 'ChIJxxxxxxxxxx'
                ]
            ]
        ];

        // Mock for place details response
        $this->mockDetailsResponse = [
            'status' => 'OK',
            'result' => [
                'name' => 'Test High School',
                'formatted_address' => '123 Main Street, Cape Town, 8001, South Africa',
                'address_components' => [
                    ['long_name' => '123', 'short_name' => '123', 'types' => ['street_number']],
                    ['long_name' => 'Main Street', 'short_name' => 'Main St', 'types' => ['route']],
                    ['long_name' => 'Cape Town', 'short_name' => 'Cape Town', 'types' => ['locality']],
                    ['long_name' => 'Western Cape', 'short_name' => 'WC', 'types' => ['administrative_area_level_1']],
                    ['long_name' => 'South Africa', 'short_name' => 'ZA', 'types' => ['country']],
                    ['long_name' => '8001', 'short_name' => '8001', 'types' => ['postal_code']]
                ],
                'geometry' => [
                    'location' => [
                        'lat' => -33.925,
                        'lng' => 18.424
                    ]
                ]
            ]
        ];
    }

    public function testAutocompleteSearch()
    {
        Http::fake(['*' => Http::response($this->mockAutocompleteResponse, 200)]);

        $result = json_decode(
            $this->service->searchAddress('123 Main Street')
                ->get(),
            true
        );

        $this->assertAutocompleteResponseStructure($result);
        Http::assertSent(function ($request) {
            return $request['query'] === '123 Main Street';
        });
    }

    public function testHighSchoolSearch()
    {
        Http::fake(['*' => Http::response($this->mockAutocompleteResponse, 200)]);

        $this->service->searchHighSchools('Test High School')
            ->get();

        Http::assertSent(function ($request) {
            return $request['type'] === 'secondary_school';
        });
    }

    public function testPlaceDetails()
    {
        Http::fake(['*' => Http::response($this->mockDetailsResponse, 200)]);

        $result = json_decode(
            $this->service->getPlaceDetails('ChIJxxxxxxxxxx')->get(),
            true
        );

        $this->assertPlaceDetailsResponseStructure($result);
        Http::assertSent(function ($request) {
            return $request['place_id'] === 'ChIJxxxxxxxxxx';
        });
    }

    public function testSearchWithLocation()
    {
        Http::fake(['*' => Http::response($this->mockAutocompleteResponse, 200)]);

        $this->service->searchAddress('123 Main Street')
            ->location(-33.925, 18.424)
            ->get();

        Http::assertSent(function ($request) {
            return $request['location'] === '-33.925,18.424';
        });
    }

    public function testSearchWithError()
    {
        Http::fake(['*' => Http::response(['status' => 'ZERO_RESULTS'], 200)]);

        $this->expectException(GoogleMapsException::class);
        $this->service->searchAddress('Test')->get();
    }

    public function testSearchWithNoResults()
    {
        Http::fake(['*' => Http::response([
            'status' => 'OK',
            'predictions' => []
        ], 200)]);

        $result = json_decode(
            $this->service->searchAddress('NonexistentAddress')->get(),
            true
        );

        $this->assertEmpty($result);
    }

    private function assertAutocompleteResponseStructure($result)
    {
        $this->assertIsArray($result);
        if (!empty($result)) {
            $this->assertArrayHasKey('place_id', $result[0]);
            $this->assertArrayHasKey('name', $result[0]);
        }
    }

    private function assertPlaceDetailsResponseStructure($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('coordinates', $result);

        $this->assertArrayHasKey('line1', $result['address']);
        $this->assertArrayHasKey('line2', $result['address']);
        $this->assertArrayHasKey('street_number', $result['address']);
        $this->assertArrayHasKey('street_name', $result['address']);
        $this->assertArrayHasKey('city', $result['address']);
        $this->assertArrayHasKey('province', $result['address']);
        $this->assertArrayHasKey('country', $result['address']);
    }
}
