<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Data;

class LocationIdData extends Data
{
    public function __construct(
        public readonly string $locationId,
        public readonly ?string $provider = null,
    ) {}
}
