<?php

return [
    // Base URL of Huvant Meeting Minutes; empty when it shares the ERP domain
    // (the default: Minutes under /riunioni, its API under /api).
    'url' => env('HUVANT_MEETINGS_URL', ''),

    // Shared with Minutes (ERP_SSO_SECRET): signs the short-lived login token.
    'sso_secret' => env('HUVANT_MEETINGS_SSO_SECRET'),

    'sso_ttl_seconds' => (int) env('HUVANT_MEETINGS_SSO_TTL', 60),
];
