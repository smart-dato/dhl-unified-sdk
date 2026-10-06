<?php

return [
    'api_key' => env('DHL_UNIFIED_API_KEY'),
    'base_url' => env('DHL_UNIFIED_BASE_URL', 'https://api.dhl.com'),
    // pause between two requests, the Location Finder starts at 500 calls per day
    'delay_ms' => (int) env('DHL_UNIFIED_DELAY_MS', 0),
];
