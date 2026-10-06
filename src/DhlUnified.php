<?php

namespace SmartDato\DhlUnified;

use SmartDato\DhlUnified\Connectors\DhlUnifiedConnector;
use SmartDato\DhlUnified\Resources\LocationsResource;

class DhlUnified
{
    private ?LocationsResource $locationsResource = null;

    public function __construct(
        protected DhlUnifiedConnector $connector,
    ) {}

    public function locations(): LocationsResource
    {
        return $this->locationsResource ??= new LocationsResource($this->connector);
    }

    /**
     * @param array{
     *     api_key: string,
     *     base_url?: string|null,
     *     delay_ms?: int|null,
     * } $config
     */
    public static function make(array $config): self
    {
        return new self(new DhlUnifiedConnector(
            apiKey: $config['api_key'],
            baseUrl: $config['base_url'] ?? DhlUnifiedConnector::PRODUCTION_URL,
            delayInMilliseconds: $config['delay_ms'] ?? 0,
        ));
    }
}
