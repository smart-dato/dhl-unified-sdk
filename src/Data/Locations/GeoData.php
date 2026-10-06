<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Data;

class GeoData extends Data
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
    ) {}
}
