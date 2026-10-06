<?php

namespace SmartDato\DhlUnified\Connectors;

use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use SmartDato\DhlUnified\Exceptions\DhlUnifiedApiException;
use SmartDato\DhlUnified\Exceptions\RateLimitExceededException;
use Throwable;

class DhlUnifiedConnector extends Connector
{
    use AcceptsJson;
    use AlwaysThrowOnErrors;

    public const string PRODUCTION_URL = 'https://api.dhl.com';

    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = self::PRODUCTION_URL,
        protected int $delayInMilliseconds = 0,
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultAuth(): HeaderAuthenticator
    {
        return new HeaderAuthenticator($this->apiKey, 'DHL-API-Key');
    }

    protected function defaultDelay(): ?int
    {
        return $this->delayInMilliseconds > 0 ? $this->delayInMilliseconds : null;
    }

    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return $response->status() === 429
            ? RateLimitExceededException::fromResponse($response)
            : DhlUnifiedApiException::fromResponse($response);
    }
}
