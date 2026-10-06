# DHL Unified SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-dato/dhl-unified-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/dhl-unified-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/smart-dato/dhl-unified-sdk.svg?style=flat-square)](https://packagist.org/packages/smart-dato/dhl-unified-sdk)

Laravel SDK, built on [Saloon](https://docs.saloon.dev), for the DHL developer portal APIs that authenticate with a plain `DHL-API-Key` header.

Supported today:

- [Location Finder - Unified](https://developer.dhl.com/api-reference/location-finder): find DHL Packstations, post offices and service points around a point or an address.

## Installation

```bash
composer require smart-dato/dhl-unified-sdk
```

Publish the config file:

```bash
php artisan vendor:publish --tag="dhl-unified-sdk-config"
```

```php
return [
    'api_key' => env('DHL_UNIFIED_API_KEY'),
    'base_url' => env('DHL_UNIFIED_BASE_URL', 'https://api.dhl.com'),
    'delay_ms' => (int) env('DHL_UNIFIED_DELAY_MS', 0),
];
```

The API key comes from an app on [developer.dhl.com](https://developer.dhl.com) that is subscribed to the APIs you call.

## Usage

```php
use SmartDato\DhlUnified\DhlUnified;
use SmartDato\DhlUnified\Enums\LocationType;

$dhl = app(DhlUnified::class); // from config
$dhl = DhlUnified::make(['api_key' => '...', 'delay_ms' => 1000]); // explicit

$lockers = $dhl->locations()->findByGeo(
    latitude: 50.72,
    longitude: 7.11,
    countryCode: 'DE',
    radiusInMeters: 5000,
    limit: 50,
    locationType: LocationType::Locker,
);

$lockers[0]->id();                   // "8003-4103145", id of the unified record
$lockers[0]->location->keyword;      // "Packstation"
$lockers[0]->location->keywordId;    // "145", used when addressing a shipment
$lockers[0]->place->address->postalCode;

$dhl->locations()->findByAddress(countryCode: 'DE', postalCode: '53113');
$dhl->locations()->get('8003-4103145');
```

The Location Finder returns the nearest locations up to `limit` (max 50). It has no paging and no bulk download.

### Errors

Every non-2xx response throws `SmartDato\DhlUnified\Exceptions\DhlUnifiedApiException`. A `429` throws its subclass `RateLimitExceededException`, so callers can stop and resume once the quota resets (the initial service level is 500 calls per day).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
