# Laravel Google Maps

![Code Checks](https://github.com/sacapsystems/laravel-google-maps/actions/workflows/code-checks.yaml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-%5E7.2-777BB4?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-%5E6.0-FF2D20?logo=laravel)
![Code Style](https://img.shields.io/badge/Code%20Style-PSR--12-green)

A Laravel package for Google Maps integration.

## Table of Contents
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Response Format](#response-format)
- [Package Development](#package-development)
    - [Testing](#testing)
    - [Code Style](#code-style)

## Requirements

This package requires:

- PHP 7.4 or higher
- Laravel 6.0 or higher
- A Google Cloud Platform account
- A valid Google Maps API key

## Installation

First, add the repository to your composer.json:

```json
"repositories": [
        {
                "type": "vcs",
                "url": "https://github.com/sacapsystems/laravel-google-maps.git"
        }
]
```

Then, install the package via composer:

```bash
composer require sacapsystems/laravel-google-maps
```

If you're not using package discovery, add the service provider and facade to your `config/app.php`:

```php
'providers' => [
        // ...
        Sacapsystems\LaravelGoogleMaps\GoogleMapsServiceProvider::class,
],

'aliases' => [
        // ...
        'GoogleMaps' => Sacapsystems\LaravelGoogleMaps\Facades\GoogleMaps::class,
]
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Sacapsystems\LaravelGoogleMaps\GoogleMapsServiceProvider"
```

Add your Google Maps API key to your .env file:

```
GOOGLE_MAPS_API_KEY=your-key-here
```

## Usage

```php
use Sacapsystems\LaravelGoogleMaps\Facades\GoogleMaps;

// Get coordinates for an address (default limit is 5)
$coordinates = GoogleMaps::searchAddress('123 Main Street, Cape Town')
                        ->get();

// Get coordinates with custom limit
$coordinates = GoogleMaps::searchAddress('123 Main Street, Cape Town')
                        ->limit(3)
                        ->get();

// Search for schools (default limit is 5)
$schools = GoogleMaps::searchHighSchools('Cape Town High School')
                        ->get();

// Search for schools with custom limit
$schools = GoogleMaps::searchHighSchools('Cape Town High School')
                        ->limit(10)
                        ->get();

// Get details for a specific place
$place = GoogleMaps::getPlaceDetails('place_id')
                   ->get();
```
## Response Format
The API returns different response formats depending on the method used:

### Search Results (searchAddress & searchHighSchools)
```json
{
    "place_id": "ChIJ..",
    "name": "Cape Town High School, Hatfield Street, Gardens, Cape Town, South Africa"
}
```

### Place Details
```json
{
    "name": "Place Name",
    "address": {
        "line1": "123 Main Street",
        "line2": "Unit 1",
        "street_number": "123",
        "street_name": "Main Street",
        "suburb": "Gardens",
        "city": "Cape Town",
        "postalCode": "8001",
        "province": "Western Cape",
        "provinceCode": "WC",
        "country": "South Africa",
        "countryCode": "ZA"
    },
    "coordinates": {
        "lat": -33.123456,
        "lng": 18.123456
    }
}
```

## Package Development

The following sections are for package developers only.

### Testing
Run the package tests with:

```bash
composer test
```

### Code Style
This package follows PSR-12 coding standards. Package developers can check and fix the code style with:

```bash
# Check code style
./vendor/bin/phpcs

# Fix code style
./vendor/bin/phpcbf
```

Or use composer scripts:

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```
