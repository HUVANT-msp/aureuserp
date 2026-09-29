<?php

namespace Huvant\Meetings\Listeners;

use Illuminate\Support\Facades\Cookie;

/** Logging out of the ERP also ends the Minutes session on the shared domain. */
class EndMeetingsSession
{
    public const COOKIES = ['huvant_session', 'huvant_csrf'];

    public function handle(): void
    {
        foreach (self::COOKIES as $name) {
            Cookie::queue(Cookie::forget($name));
        }
    }
}
