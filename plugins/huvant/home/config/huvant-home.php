<?php

return [
    // Milo's brief comes from the Minutes API, signed like the bridge webhook.
    // Default: the webhook URL with /erp/webhook replaced by /erp/milo-briefing.
    'milo_url' => env('HUVANT_MILO_BRIEFING_URL'),

    'language' => env('HUVANT_MILO_BRIEFING_LANGUAGE', 'en'),

    'timeout' => (int) env('HUVANT_MILO_BRIEFING_TIMEOUT', 90),

    // When the briefs are written (Europe/Rome, weekdays).
    'times' => ['08:00', '13:00'],
];
