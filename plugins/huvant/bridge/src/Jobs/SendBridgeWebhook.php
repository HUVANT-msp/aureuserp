<?php

namespace Huvant\Bridge\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SendBridgeWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /** @param array<string, mixed> $payload */
    public function __construct(public array $payload) {}

    /** @return array<int, int> */
    public function backoff(): array
    {
        return [30, 120, 300, 900];
    }

    public function handle(): void
    {
        $url = config('huvant-bridge.webhook.url');
        $secret = config('huvant-bridge.webhook.secret');

        if (! is_string($url) || $url === '' || ! is_string($secret) || $secret === '') {
            return;
        }

        $body = json_encode($this->payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $timestamp = (string) now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, $secret);

        $response = Http::timeout((int) config('huvant-bridge.webhook.timeout', 10))
            ->withHeaders([
                'Content-Type'       => 'application/json',
                'X-Huvant-Timestamp' => $timestamp,
                'X-Huvant-Signature' => 'sha256='.$signature,
            ])
            ->withBody($body, 'application/json')
            ->post($url);

        if ($response->successful() || $response->status() === 409) {
            return;
        }

        $exception = new RuntimeException("Huvant bridge webhook failed with HTTP {$response->status()}.");

        if ($response->status() === 429 || $response->serverError()) {
            throw $exception;
        }

        $this->fail($exception);
    }
}
