<?php

namespace SmartDato\DhlUnified\Exceptions;

/**
 * HTTP 429: the per-second or the daily quota of the API key is used up.
 */
class RateLimitExceededException extends DhlUnifiedApiException {}
