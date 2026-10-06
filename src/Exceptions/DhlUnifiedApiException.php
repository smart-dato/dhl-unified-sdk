<?php

namespace SmartDato\DhlUnified\Exceptions;

use Exception;
use Saloon\Http\Response;
use Throwable;

/**
 * The DHL developer portal APIs answer errors as RFC 7807 problem details
 * ({title, status, detail, instance}).
 */
class DhlUnifiedApiException extends Exception
{
    final public function __construct(
        string $message = '',
        int $code = 0,
        public readonly ?string $detail = null,
        public readonly ?string $instance = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(Response $response): static
    {
        try {
            $data = $response->json();
        } catch (Throwable) {
            $data = [];
        }

        $title = $data['title'] ?? "DHL API error: {$response->status()}";
        $detail = $data['detail'] ?? null;

        return new static(
            message: is_null($detail) ? $title : "{$title}: {$detail}",
            code: $response->status(),
            detail: $detail,
            instance: $data['instance'] ?? null,
        );
    }
}
