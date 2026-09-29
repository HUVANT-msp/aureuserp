<?php

namespace Huvant\Meetings\Filament\Pages;

use Filament\Pages\Page;
use Huvant\Meetings\Support\MeetingsSso;

abstract class EmbeddedMeetingsPage extends Page
{
    protected string $view = 'huvant-meetings::filament.pages.embedded';

    public ?string $frameUrl = null;

    public ?string $directUrl = null;

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

        // Two separate single-use links: one for the frame, one for "open in a new tab".
        $this->frameUrl = MeetingsSso::url(auth()->user()->email, $this->target());
        $this->directUrl = MeetingsSso::url(auth()->user()->email, $this->target());
    }
}
