<?php

namespace Sacapsystems\LaravelAzureMaps\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Sacapsystems\LaravelAzureMaps\Services\AzureMapsService;
use Sacapsystems\LaravelAzureMaps\Tests\TestCase;

class AzureMapsServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AzureMapsService();
    }

    public function testSearchAddress()
    {
        Http::fake([
            '*' => Http::response([
                'summary' => ['numResults' => 1],
                'results' => [[
                    'address' => [
                        'streetNumber' => '123',
                        'streetName' => 'Main Street',
                        'municipality' => 'Cape Town',
                        'countrySubdivision' => 'Western Cape',
                        'countrySubdivisionCode' => 'WC',
                        'country' => 'South Africa',
                        'countryCodeISO3' => 'ZAF',
                        'postalCode' => '8001',
                        'municipalitySubdivision' => 'City Bowl'
                    ],
                    'position' => [
                        'lat' => -33.925,
                        'lon' => 18.424
                    ],
                    'poi' => [
                        'name' => 'Test Location'
                    ]
                ]]
            ], 200)
        ]);

        $result = json_decode($this->service->searchAddress('123 Main Street, Cape Town'), true);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Test Location', $result[0]['name']);
        $this->assertEquals('123 Main Street', $result[0]['address']['line1']);
        $this->assertEquals(-33.925, $result[0]['coordinates']['lat']);
        $this->assertEquals(18.424, $result[0]['coordinates']['lng']);
    }

    public function testSearchSchools()
    {
        Http::fake([
            '*' => Http::response([
                'summary' => ['numResults' => 1],
                'results' => [[
                    'address' => [
                        'streetNumber' => '1',
                        'streetName' => 'School Street',
                        'municipality' => 'Cape Town',
                        'countrySubdivision' => 'Western Cape',
                        'countrySubdivisionCode' => 'WC',
                        'country' => 'South Africa',
                        'countryCodeISO3' => 'ZAF',
                        'postalCode' => '8001',
                        'municipalitySubdivision' => 'City Bowl'
                    ],
                    'position' => [
                        'lat' => -33.925,
                        'lon' => 18.424
                    ],
                    'poi' => [
                        'name' => 'Cape Town High School'
                    ]
                ]]
            ], 200)
        ]);

        $result = json_decode($this->service->searchSchools('Cape Town High School'), true);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Cape Town High School', $result[0]['name']);
        $this->assertEquals('1 School Street', $result[0]['address']['line1']);
    }

    public function testSearchWithNoResults()
    {
        Http::fake([
            '*' => Http::response([
                'summary' => ['numResults' => 0],
                'results' => []
            ], 200)
        ]);

        $result = json_decode($this->service->searchAddress('NonexistentAddress'), true);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSearchWithError()
    {
        Http::fake([
            '*' => Http::response([], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch search results');

        $this->service->searchAddress('Test Address');
    }
}
