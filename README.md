# Laravel Azure Maps

![Code Checks](https://github.com/sacapsystems/laravel-azure-maps/actions/workflows/code-checks.yaml/badge.svg)
![GitHub Latest Tag](https://img.shields.io/github/v/tag/sacapsystems/laravel-azure-maps?label=stable)
![PHP](https://img.shields.io/badge/PHP-%5E7.2-777BB4?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-%5E6.0-FF2D20?logo=laravel)
![Code Style](https://img.shields.io/badge/code%20style-PSR--12-green)

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
## Development

### Testing
Run the tests with:

```bash
composer test
```

## Code Style
This package follows PSR-12 coding standards. You can check and fix the code style with:

```bash
# Check code style
./vendor/bin/phpcs

# Fix code style
./vendor/bin/phpcbf
```
You can also use composer scripts:

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```
## Requirements

This package requires:

- PHP 7.2 or higher
- Laravel 6.0 or higher
- An active Azure Maps subscription
- A valid Azure Maps API key

