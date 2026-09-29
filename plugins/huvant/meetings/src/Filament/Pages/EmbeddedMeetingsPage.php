<?php

namespace Huvant\Meetings\Filament\Pages;

use Filament\Pages\Page;
use Huvant\Meetings\Support\MeetingsSso;

abstract class EmbeddedMeetingsPage extends Page
{
    protected string $view = 'huvant-meetings::filament.pages.embedded';

    public ?string $frameUrl = null;

    public ?string $directUrl = null;

    /** Set when the ERP is opened over plain HTTP: embedding needs HTTPS. */
    public ?string $secureUrl = null;

    abstract protected function target(): string;

    public static function getNavigationGroup(): string
    {
        return 'Riunioni';
    }

    public function mount(): void
    {
        if (! MeetingsSso::isConfigured() || ! auth()->user()) {
            return;
        }

        // Minutes' session cookie and the Canvas microphone only work in a frame
        // when both sides are HTTPS (same site, secure context).
        $appUrl = rtrim((string) config('app.url'), '/');
        if (! request()->isSecure() && str_starts_with($appUrl, 'https://')) {
            $this->secureUrl = $appUrl.'/'.ltrim(request()->path(), '/');

            return;
        }

        // Two separate single-use links: one for the frame, one for "open in a new tab".
        $this->frameUrl = MeetingsSso::url(auth()->user()->email, $this->target());
        $this->directUrl = MeetingsSso::url(auth()->user()->email, $this->target());
    }
}
