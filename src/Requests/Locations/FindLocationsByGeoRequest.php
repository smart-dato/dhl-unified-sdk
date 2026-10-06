<?php

namespace SmartDato\DhlUnified\Requests\Locations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use SmartDato\DhlUnified\Enums\LocationType;

class FindLocationsByGeoRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly float $latitude,
        protected readonly float $longitude,
        protected readonly ?string $countryCode = null,
        protected readonly ?int $radiusInMeters = null,
        protected readonly ?int $limit = null,
        protected readonly ?LocationType $locationType = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/location-finder/v1/find-by-geo';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'countryCode' => $this->countryCode,
            'radius' => $this->radiusInMeters,
            'limit' => $this->limit,
            'locationType' => $this->locationType?->value,
        ], fn (mixed $value): bool => ! is_null($value));
    }
}
