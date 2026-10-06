<?php

namespace SmartDato\DhlUnified\Resources;

use Saloon\Http\Response;
use SmartDato\DhlUnified\Data\Locations\LocationData;
use SmartDato\DhlUnified\Enums\LocationType;
use SmartDato\DhlUnified\Requests\Locations\FindLocationsByAddressRequest;
use SmartDato\DhlUnified\Requests\Locations\FindLocationsByGeoRequest;
use SmartDato\DhlUnified\Requests\Locations\GetLocationRequest;

/**
 * Location Finder Unified: DHL returns the nearest locations around a point or
 * an address, capped by `limit`. There is no paging and no bulk download.
 */
class LocationsResource extends BaseResource
{
    /**
     * @return list<LocationData>
     */
    public function findByGeo(
        float $latitude,
        float $longitude,
        ?string $countryCode = null,
        ?int $radiusInMeters = null,
        ?int $limit = null,
        ?LocationType $locationType = null,
    ): array {
        return $this->locations($this->send(new FindLocationsByGeoRequest(
            latitude: $latitude,
            longitude: $longitude,
            countryCode: $countryCode,
            radiusInMeters: $radiusInMeters,
            limit: $limit,
            locationType: $locationType,
        )));
    }

    /**
     * @return list<LocationData>
     */
    public function findByAddress(
        string $countryCode,
        ?string $postalCode = null,
        ?string $addressLocality = null,
        ?string $streetAddress = null,
        ?int $radiusInMeters = null,
        ?int $limit = null,
        ?LocationType $locationType = null,
    ): array {
        return $this->locations($this->send(new FindLocationsByAddressRequest(
            countryCode: $countryCode,
            postalCode: $postalCode,
            addressLocality: $addressLocality,
            streetAddress: $streetAddress,
            radiusInMeters: $radiusInMeters,
            limit: $limit,
            locationType: $locationType,
        )));
    }

    public function get(string $id): LocationData
    {
        return LocationData::from($this->send(new GetLocationRequest($id))->json());
    }

    /**
     * @return list<LocationData>
     */
    private function locations(Response $response): array
    {
        return array_values(LocationData::collect($response->json('locations') ?? []));
    }
}
