<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Data;

class LocationData extends Data
{
    /**
     * @param  list<array<string, mixed>>  $openingHours
     * @param  list<array<string, mixed>>  $closurePeriods
     * @param  list<string>  $serviceTypes
     */
    public function __construct(
        public readonly string $url,
        public readonly LocationInfoData $location,
        public readonly ?string $name = null,
        public readonly ?float $distance = null,
        public readonly ?PlaceData $place = null,
        public readonly array $openingHours = [],
        public readonly array $closurePeriods = [],
        public readonly array $serviceTypes = [],
    ) {}

    /**
     * The id of the unified record (`/locations/{id}`). DHL merges the
     * per-division entries of `location.ids` into this one record.
     */
    public function id(): string
    {
        return basename($this->url);
    }
}
