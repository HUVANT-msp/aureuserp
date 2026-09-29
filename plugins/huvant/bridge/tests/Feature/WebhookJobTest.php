<?php

use Huvant\Bridge\Jobs\SendBridgeWebhook;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

it('signs the timestamp and raw webhook body expected by the receiver', function () {
    Carbon::setTestNow('2026-09-29 12:00:00 UTC');
    config([
        'huvant-bridge.webhook.url'    => 'https://minutes.test/api/v1/erp/webhook',
        'huvant-bridge.webhook.secret' => 'test-secret',
    ]);
    Http::fake(['*' => Http::response(status: 204)]);

    $payload = [
        'event_id'       => '2abcf57b-cb1c-474b-a22e-6383d1e58ec1',
        'resource_type'  => 'task',
        'erp_id'         => 42,
        'event_type'     => 'updated',
        'updated_at'     => '2026-09-29T12:00:00+00:00',
        'changed_fields' => ['state'],
    ];

    (new SendBridgeWebhook($payload))->handle();

    Http::assertSent(function (Request $request) use ($payload): bool {
        $body = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $timestamp = (string) Carbon::now()->timestamp;

        return $request->url() === 'https://minutes.test/api/v1/erp/webhook'
            && $request->body() === $body
            && $request->hasHeader('X-Huvant-Timestamp', $timestamp)
            && $request->hasHeader('X-Huvant-Signature', 'sha256='.hash_hmac('sha256', $timestamp.'.'.$body, 'test-secret'));
    });
});

it('accepts a duplicate event response as an idempotent delivery', function () {
    config([
        'huvant-bridge.webhook.url'    => 'https://minutes.test/api/v1/erp/webhook',
        'huvant-bridge.webhook.secret' => 'test-secret',
    ]);
    Http::fake(['*' => Http::response(['detail' => 'duplicate event'], 409)]);

    (new SendBridgeWebhook(['event_id' => 'duplicate']))->handle();

    Http::assertSentCount(1);
});
