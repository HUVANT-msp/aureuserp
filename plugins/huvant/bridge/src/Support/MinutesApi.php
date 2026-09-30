<?php

namespace Huvant\Bridge\Support;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Signed calls to the Minutes API (/api/v1/erp/...), same signature as the
 * bridge webhook: HMAC-SHA256 of "timestamp.body" with the shared secret.
 */
class MinutesApi
{
    public static function url(string $path): ?string
    {
        $webhook = config('huvant-bridge.webhook.url');

        return is_string($webhook) && str_contains($webhook, '/erp/webhook')
            ? str_replace('/erp/webhook', '/erp/'.ltrim($path, '/'), $webhook)
            : null;
    }

    /** @return array<string, mixed> */
    public static function post(string $path, array $payload, int $timeout = 8): array
    {
        return (array) static::send($path, $payload, $timeout)->json();
    }

    /** The raw response (e.g. a PDF), already checked for errors. */
    public static function send(string $path, array $payload, int $timeout = 8): Response
    {
        $url = static::url($path);
        $secret = (string) config('huvant-bridge.webhook.secret');
        if (! $url || $secret === '') {
            throw new RuntimeException('Meetings are not connected.');
        }
        $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $timestamp = (string) now()->timestamp;

        return Http::timeout($timeout)
            ->withHeaders([
                'X-Huvant-Timestamp' => $timestamp,
                'X-Huvant-Signature' => 'sha256='.hash_hmac('sha256', $timestamp.'.'.$body, $secret),
            ])
            ->withBody($body, 'application/json')
            ->post($url)->throw();
    }
}
