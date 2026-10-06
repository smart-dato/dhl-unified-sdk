<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use SmartDato\DhlUnified\Connectors\DhlUnifiedConnector;
use SmartDato\DhlUnified\Data\Locations\LocationData;
use SmartDato\DhlUnified\DhlUnified;
use SmartDato\DhlUnified\Enums\LocationType;
use SmartDato\DhlUnified\Exceptions\DhlUnifiedApiException;
use SmartDato\DhlUnified\Exceptions\RateLimitExceededException;
use SmartDato\DhlUnified\Requests\Locations\FindLocationsByAddressRequest;
use SmartDato\DhlUnified\Requests\Locations\FindLocationsByGeoRequest;
use SmartDato\DhlUnified\Requests\Locations\GetLocationRequest;
use SmartDato\DhlUnified\Resources\LocationsResource;

afterEach(function () {
    MockClient::destroyGlobal();
});

function locationsResource(MockClient $mockClient): LocationsResource
{
    $connector = new DhlUnifiedConnector('test-key', 'https://api.test');
    $connector->withMockClient($mockClient);

    return new LocationsResource($connector);
}

it('finds locations around a point', function () {
    $mockClient = new MockClient([
        FindLocationsByGeoRequest::class => MockResponse::make(fixtureJson('Locations/find-by-geo')),
    ]);

    $locations = locationsResource($mockClient)->findByGeo(
        latitude: 50.72,
        longitude: 7.11,
        countryCode: 'DE',
        radiusInMeters: 5000,
        limit: 50,
        locationType: LocationType::Locker,
    );

    expect($locations)->toHaveCount(2)->each->toBeInstanceOf(LocationData::class);

    $mockClient->assertSent(fn (Request $request): bool => $request->resolveEndpoint() === '/location-finder/v1/find-by-geo'
        && $request->query()->all() === [
            'latitude' => 50.72,
            'longitude' => 7.11,
            'countryCode' => 'DE',
            'radius' => 5000,
            'limit' => 50,
            'locationType' => 'locker',
        ]);
});

it('maps the location payload', function () {
    $mockClient = new MockClient([
        MockResponse::make(fixtureJson('Locations/find-by-geo')),
    ]);

    $location = locationsResource($mockClient)->findByGeo(50.72, 7.11)[0];

    expect($location->id())->toBe('8003-4103145')
        ->and($location->name)->toBe('Packstation 145')
        ->and($location->distance)->toBe(120.0)
        ->and($location->serviceTypes)->toBe(['parcel:pick-up-registered', 'parcel:drop-off'])
        ->and($location->location->keyword)->toBe('Packstation')
        ->and($location->location->keywordId)->toBe('145')
        ->and($location->location->type)->toBe(LocationType::Locker->value)
        ->and($location->location->ids[0]->locationId)->toBe('8003-4103145')
        ->and($location->location->ids[0]->provider)->toBe('parcel')
        ->and($location->place->address->countryCode)->toBe('DE')
        ->and($location->place->address->postalCode)->toBe('53113')
        ->and($location->place->address->addressLocality)->toBe('Bonn')
        ->and($location->place->address->streetAddress)->toBe('Charles-de-Gaulle-Str. 20')
        ->and($location->place->geo->latitude)->toBe(50.7134)
        ->and($location->place->geo->longitude)->toBe(7.1294);
});

it('only sends the given filters', function () {
    $mockClient = new MockClient([
        MockResponse::make(['locations' => []]),
    ]);

    $locations = locationsResource($mockClient)->findByGeo(50.72, 7.11);

    expect($locations)->toBe([]);

    $mockClient->assertSent(fn (Request $request): bool => $request->query()->all() === [
        'latitude' => 50.72,
        'longitude' => 7.11,
    ]);
});

it('finds locations around an address', function () {
    $mockClient = new MockClient([
        FindLocationsByAddressRequest::class => MockResponse::make(fixtureJson('Locations/find-by-geo')),
    ]);

    $locations = locationsResource($mockClient)->findByAddress(
        countryCode: 'DE',
        postalCode: '53113',
        addressLocality: 'Bonn',
        locationType: LocationType::PostOffice,
    );

    expect($locations)->toHaveCount(2);

    $mockClient->assertSent(fn (Request $request): bool => $request->resolveEndpoint() === '/location-finder/v1/find-by-address'
        && $request->query()->all() === [
            'countryCode' => 'DE',
            'postalCode' => '53113',
            'addressLocality' => 'Bonn',
            'locationType' => 'postoffice',
        ]);
});

it('gets a single location by its id', function () {
    $mockClient = new MockClient([
        GetLocationRequest::class => MockResponse::make(fixtureJson('Locations/find-by-geo')['locations'][1]),
    ]);

    $location = locationsResource($mockClient)->get('8003-4102502');

    expect($location->id())->toBe('8003-4102502')
        ->and($location->location->type)->toBe(LocationType::PostOffice->value);

    $mockClient->assertSent(fn (Request $request): bool => $request->resolveEndpoint() === '/location-finder/v1/locations/8003-4102502');
});

it('authenticates with the DHL-API-Key header', function () {
    $mockClient = new MockClient([
        MockResponse::make(['locations' => []]),
    ]);

    $resource = locationsResource($mockClient);
    $resource->findByGeo(50.72, 7.11);

    expect($resource->lastResponse->getPendingRequest()->headers()->get('DHL-API-Key'))->toBe('test-key');
});

it('throws a rate limit exception on 429', function () {
    $mockClient = new MockClient([
        MockResponse::make(['title' => 'Too many requests', 'status' => 429, 'detail' => 'Daily quota reached.'], 429),
    ]);

    expect(fn () => locationsResource($mockClient)->findByGeo(50.72, 7.11))
        ->toThrow(RateLimitExceededException::class, 'Too many requests: Daily quota reached.');
});

it('throws an api exception on other errors', function () {
    $mockClient = new MockClient([
        MockResponse::make(['title' => 'Unauthorized', 'status' => 401], 401),
    ]);

    try {
        locationsResource($mockClient)->findByGeo(50.72, 7.11);
        $this->fail('Expected a DhlUnifiedApiException.');
    } catch (DhlUnifiedApiException $exception) {
        expect($exception)->not->toBeInstanceOf(RateLimitExceededException::class)
            ->and($exception->getMessage())->toBe('Unauthorized')
            ->and($exception->getCode())->toBe(401);
    }
});

it('builds the client from the package config', function () {
    config()->set('dhl-unified-sdk.api_key', 'config-key');
    config()->set('dhl-unified-sdk.base_url', 'https://api.config.test');

    $mockClient = MockClient::global([
        MockResponse::make(['locations' => []]),
    ]);

    app(DhlUnified::class)->locations()->findByGeo(50.72, 7.11);

    $pendingRequest = $mockClient->getLastPendingRequest();

    expect($pendingRequest->getUrl())->toStartWith('https://api.config.test/location-finder/v1/find-by-geo')
        ->and($pendingRequest->headers()->get('DHL-API-Key'))->toBe('config-key');

});

it('builds the client from an array', function () {
    $mockClient = MockClient::global([
        MockResponse::make(['locations' => []]),
    ]);

    DhlUnified::make(['api_key' => 'array-key'])->locations()->findByGeo(50.72, 7.11);

    expect($mockClient->getLastPendingRequest()->headers()->get('DHL-API-Key'))->toBe('array-key');

});
