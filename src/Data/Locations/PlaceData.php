<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Data;

class PlaceData extends Data
{
    public function __construct(
        public readonly ?AddressData $address = null,
        public readonly ?GeoData $geo = null,
    ) {}
}
