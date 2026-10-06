<?php

namespace SmartDato\DhlUnified\Requests\Locations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use SmartDato\DhlUnified\Enums\LocationType;

class FindLocationsByAddressRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $countryCode,
        protected readonly ?string $postalCode = null,
        protected readonly ?string $addressLocality = null,
        protected readonly ?string $streetAddress = null,
        protected readonly ?int $radiusInMeters = null,
        protected readonly ?int $limit = null,
        protected readonly ?LocationType $locationType = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/location-finder/v1/find-by-address';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            'countryCode' => $this->countryCode,
            'postalCode' => $this->postalCode,
            'addressLocality' => $this->addressLocality,
            'streetAddress' => $this->streetAddress,
            'radius' => $this->radiusInMeters,
            'limit' => $this->limit,
            'locationType' => $this->locationType?->value,
        ], fn (mixed $value): bool => ! is_null($value));
    }
}
