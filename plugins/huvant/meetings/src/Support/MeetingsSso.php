<?php

namespace Huvant\Meetings\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Signed, single-use login link from the ERP into Huvant Meeting Minutes.
 *
 * Token = base64url(json claims) "." base64url(HMAC-SHA256(secret, first part)).
 * Minutes verifies signature, expiry and nonce (single use), then opens a
 * session for the same e-mail and redirects to "next".
 */
class MeetingsSso
{
    /** Where Minutes lives on the shared Huvant domain. */
    public const HOME = '/riunioni/';

    public static function isConfigured(): bool
    {
        return filled(config('huvant-meetings.sso_secret'));
    }

    public static function url(string $email, string $next = self::HOME): string
    {
        // Empty on the shared domain: the link stays relative.
        $base = rtrim((string) config('huvant-meetings.url'), '/');

        return $base.'/api/v1/auth/erp-sso?token='.urlencode(static::token($email, $next));
    }

    public static function token(string $email, string $next = '/', ?int $now = null, ?string $nonce = null): string
    {
        if (! str_starts_with($next, '/') || str_starts_with($next, '//')) {
            throw new InvalidArgumentException('The target must be a local path.');
        }

        $claims = [
            'email' => Str::lower(trim($email)),
            'exp'   => ($now ?? time()) + max(10, (int) config('huvant-meetings.sso_ttl_seconds', 60)),
            'nonce' => $nonce ?? Str::random(32),
            'next'  => $next,
        ];
        $payload = static::base64Url(json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
        $signature = hash_hmac('sha256', $payload, (string) config('huvant-meetings.sso_secret'), true);

        return $payload.'.'.static::base64Url($signature);
    }

    private static function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
