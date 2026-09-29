<?php

return [
    'webhook' => [
        'url'     => env('HUVANT_BRIDGE_WEBHOOK_URL'),
        'secret'  => env('HUVANT_BRIDGE_WEBHOOK_SECRET'),
        'queue'   => env('HUVANT_BRIDGE_WEBHOOK_QUEUE', 'default'),
        'timeout' => (int) env('HUVANT_BRIDGE_WEBHOOK_TIMEOUT', 10),
    ],
    'idempotency' => [
        'ttl_hours' => (int) env('HUVANT_BRIDGE_IDEMPOTENCY_TTL_HOURS', 24),
    ],
];
