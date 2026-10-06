<?php

namespace SmartDato\DhlUnified\Data\Locations;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class LocationInfoData extends Data
{
    /**
     * `type` stays a string so a location type DHL adds later does not break
     * the mapping, compare it against LocationType values.
     *
     * @param  list<LocationIdData>  $ids
     */
    public function __construct(
        #[DataCollectionOf(LocationIdData::class)]
        public readonly array $ids = [],
        public readonly ?string $keyword = null,
        public readonly ?string $keywordId = null,
        public readonly ?string $type = null,
    ) {}
}
