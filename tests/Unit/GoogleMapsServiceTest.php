<?php

namespace Sacapsystems\LaravelGoogleMaps\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Sacapsystems\LaravelGoogleMaps\Exceptions\GoogleMapsException;
use Sacapsystems\LaravelGoogleMaps\Services\GoogleMapsService;
use Sacapsystems\LaravelGoogleMaps\Tests\TestCase;
use Sacapsystems\LaravelGoogleMaps\Builders\QueryBuilder;

class GoogleMapsServiceTest extends TestCase
{
    protected $service;
    protected $mockAutocompleteResponse;
    protected $mockDetailsResponse;
    protected $container;
    protected $mock;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        config([
           'google-maps.api_key' => 'test-api-key',
           'google-maps.base_url' => [
               'search' => 'https://maps.googleapis.com/maps/api/place/autocomplete/json',
               'details' => 'https://maps.googleapis.com/maps/api/place/details/json'
           ]
        ]);

        $this->container = [];
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);
        $history = Middleware::history($this->container);
        $handlerStack->push($history);
        $this->client = new Client(['handler' => $handlerStack]);

        $this->service = new GoogleMapsService(function () {
            return new QueryBuilder(
                config('google-maps.base_url'),
                config('google-maps.api_key'),
                $this->client
            );
        });

        $this->mockAutocompleteResponse = [
           'status' => 'OK',
           'predictions' => [
               [
                   'description' => '123 Main Street, Cape Town, South Africa',
                   'place_id' => 'ChIJxxxxxxxxxx'
               ]
           ]
        ];

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
                   'location' => ['lat' => -33.925, 'lng' => 18.424]
               ]
           ]
        ];
    }

    public function testAutocompleteSearch()
    {
        $this->mock->append(
            new Response(200, [], json_encode($this->mockAutocompleteResponse))
        );

        $result = json_decode(
            $this->service->searchAddress('123 Main Street')->get(),
            true
        );

        $this->assertAutocompleteResponseStructure($result);

        $request = $this->container[0]['request'];
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertEquals('123 Main Street', $query['input']);
    }

    public function testHighSchoolSearch()
    {
        $this->mock->append(
            new Response(200, [], json_encode($this->mockAutocompleteResponse))
        );

        $this->service->searchHighSchools('Test High School')->get();

        $request = $this->container[0]['request'];
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertEquals('secondary_school', $query['type']);
    }

    public function testPlaceDetails()
    {
        $this->mock->append(
            new Response(200, [], json_encode($this->mockDetailsResponse))
        );

        $result = json_decode(
            $this->service->getPlaceDetails('ChIJxxxxxxxxxx')->get(),
            true
        );

        $this->assertPlaceDetailsResponseStructure($result);

        $request = $this->container[0]['request'];
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertEquals('ChIJxxxxxxxxxx', $query['place_id']);
    }

    public function testSearchWithLocation()
    {
        $this->mock->append(
            new Response(200, [], json_encode($this->mockAutocompleteResponse))
        );

        $this->service->searchAddress('123 Main Street')
           ->location(-33.925, 18.424)
           ->get();

        $request = $this->container[0]['request'];
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertEquals('-33.925,18.424', $query['location']);
    }

    public function testSearchWithError()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'ZERO_RESULTS']))
        );

        $this->expectException(GoogleMapsException::class);
        $this->service->searchAddress('Test')->get();
    }

    public function testSearchWithNoResults()
    {
        $this->mock->append(
            new Response(200, [], json_encode([
               'status' => 'OK',
               'predictions' => []
            ]))
        );

        $result = json_decode(
            $this->service->searchAddress('NonexistentAddress')->get(),
            true
        );

        $this->assertEmpty($result);
    }

    public function testLimitResults()
    {
        $this->mock->append(
            new Response(200, [], json_encode($this->mockAutocompleteResponse))
        );

        $this->service->searchAddress('123 Main Street')
           ->limit(5)
           ->get();

        $request = $this->container[0]['request'];
        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertEquals(5, $query['maxResults']);
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
