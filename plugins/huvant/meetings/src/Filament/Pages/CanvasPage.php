<?php

namespace Huvant\Meetings\Filament\Pages;

use Huvant\Meetings\Support\MeetingsSso;
use Illuminate\Contracts\Support\Htmlable;

class CanvasPage extends EmbeddedToolPage
{
    public const DEFAULT_PATH = MeetingsSso::HOME.'canvas';

    protected static ?string $slug = 'meetings/canvas';

    protected static string|\UnitEnum|null $navigationGroup = 'Meetings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-microphone';

    protected static ?int $navigationSort = 2;

    public static function tool(): string
    {
        return 'canvas';
    }

    public static function getNavigationLabel(): string
    {
        return 'Canvas';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Canvas';
    }
}
