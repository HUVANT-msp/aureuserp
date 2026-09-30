<?php

namespace Huvant\Meetings\Filament\Pages;

use Huvant\Meetings\Support\MeetingsSso;
use Illuminate\Contracts\Support\Htmlable;

class MiloPage extends EmbeddedToolPage
{
    public const DEFAULT_PATH = MeetingsSso::HOME.'milo';

    protected static ?string $slug = 'milo';

    protected static string|\UnitEnum|null $navigationGroup = 'Milo';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 1;

    public static function tool(): string
    {
        return 'milo';
    }

    public static function getNavigationLabel(): string
    {
        return 'Ask Milo';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Milo';
    }
}
