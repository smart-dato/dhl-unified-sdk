<?php

namespace SmartDato\DhlUnified\Resources;

use Saloon\Http\Request;
use Saloon\Http\Response;
use SmartDato\DhlUnified\Connectors\DhlUnifiedConnector;

abstract class BaseResource
{
    public ?Request $lastRequest = null;

    public ?Response $lastResponse = null;

    public function __construct(
        protected DhlUnifiedConnector $connector,
    ) {}

    protected function send(Request $request): Response
    {
        $this->lastRequest = $request;
        $this->lastResponse = $this->connector->send($request);

        return $this->lastResponse;
    }
}
