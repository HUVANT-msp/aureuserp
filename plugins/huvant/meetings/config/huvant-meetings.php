<?php

return [
    // Base URL of Huvant Meeting Minutes as the browser reaches it. Use the
    // HTTPS address: the live Canvas needs a secure context for the microphone.
    'url' => env('HUVANT_MEETINGS_URL'),

    // Shared with Minutes (ERP_SSO_SECRET): signs the short-lived login token.
    'sso_secret' => env('HUVANT_MEETINGS_SSO_SECRET'),

    'sso_ttl_seconds' => (int) env('HUVANT_MEETINGS_SSO_TTL', 60),
];
