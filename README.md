# Laravel Azure Maps

A Laravel package for Azure Maps integration.

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Response Format](#response-format)

## Installation

You can install the package via composer:

```bash
composer require sacapsystems/laravel-azure-maps
```
## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Sacapsystems\LaravelAzureMaps\LaravelAzureMapsServiceProvider"
```

Add your Azure Maps key to your .env file:

```
AZURE_MAPS_KEY=your-key-here
```
## Usage

```php
use Sacapsystems\LaravelAzureMaps\Facades\AzureMaps;

// Get coordinates for an address (default limit is 5)
$coordinates = AzureMaps::searchAddress('123 Main Street, Cape Town');

// Get coordinates with custom limit
$coordinates = AzureMaps::searchAddress('123 Main Street, Cape Town', 2);

// Search for schools (default limit is 5)
$schools = AzureMaps::searchSchools('Cape Town High School');

// Search for schools with custom limit
$schools = AzureMaps::searchSchools('Cape Town High School', 2);
```

## Response Format
The search results will be returned in the following format:

```json
{
    "name": "Address Name",
    "address": {
        "line1": "123 Street Name",
        "line2": "Suburb, City",
        "suburb": "Suburb Name",
        "city": "City Name",
        "postalCode": "1234",
        "province": "Province Name",
        "provinceCode": "PR",
        "country": "Country Name",
        "countryCodeISO3": "CNT"
    },
    "coordinates": {
        "lat": -33.123456,
        "lng": 18.123456
    }
}
```
