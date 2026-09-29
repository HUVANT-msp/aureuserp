<?php

use Huvant\Meetings\Support\MeetingsSso;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    config([
        'huvant-meetings.url'             => 'https://minutes.test/',
        'huvant-meetings.sso_secret'      => 'shared-secret',
        'huvant-meetings.sso_ttl_seconds' => 60,
    ]);
});

// Same vector as Minutes' backend/tests/test_erp_sso.py: both sides must agree.
it('signs the login token exactly as Minutes verifies it', function () {
    expect(MeetingsSso::token('L.Pagliochini@Huvant.com', '/canvas', 1_800_000_000, '0123456789abcdef0123456789abcdef'))
        ->toBe('eyJlbWFpbCI6ImwucGFnbGlvY2hpbmlAaHV2YW50LmNvbSIsImV4cCI6MTgwMDAwMDA2MCwibm9uY2UiOiIwMTIzNDU2Nzg5YWJjZGVmMDEyMzQ1Njc4OWFiY2RlZiIsIm5leHQiOiIvY2FudmFzIn0'
            .'.3z2WL2A0ZvZs0qjroxGEiolvDV7piLjp-Mq3HEOGqqQ');
});

it('builds a login link to the Minutes SSO endpoint', function () {
    expect(MeetingsSso::url('a.gotti@huvant.com', '/'))
        ->toStartWith('https://minutes.test/api/v1/auth/erp-sso?token=');
});

it('refuses to send the browser outside Minutes', function () {
    MeetingsSso::token('a.gotti@huvant.com', 'https://evil.test');
})->throws(InvalidArgumentException::class);

it('keeps the link relative on the shared domain', function () {
    config(['huvant-meetings.url' => '']);
    expect(MeetingsSso::url('a.gotti@huvant.com'))->toStartWith('/api/v1/auth/erp-sso?token=');
});

it('reports whether the link is configured', function () {
    expect(MeetingsSso::isConfigured())->toBeTrue();
    config(['huvant-meetings.sso_secret' => null]);
    expect(MeetingsSso::isConfigured())->toBeFalse();
});
