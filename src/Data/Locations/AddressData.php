<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public readonly ?string $countryCode = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $addressLocality = null,
        public readonly ?string $streetAddress = null,
    ) {}
}
