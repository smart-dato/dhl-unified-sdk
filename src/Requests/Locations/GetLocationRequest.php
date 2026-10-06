<?php

namespace SmartDato\DhlUnified\Requests\Locations;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetLocationRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $id,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/location-finder/v1/locations/'.rawurlencode($this->id);
    }
}
