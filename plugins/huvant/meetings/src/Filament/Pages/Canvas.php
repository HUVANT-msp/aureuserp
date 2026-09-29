<?php

namespace Huvant\Meetings\Filament\Pages;

class Canvas extends EmbeddedMeetingsPage
{
    protected static ?string $slug = 'riunioni/canvas';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-microphone';

    public static function getNavigationLabel(): string
    {
        return 'Canvas';
    }

    public function getTitle(): string
    {
        return 'Meeting Canvas';
    }

    protected function target(): string
    {
        return '/canvas';
    }
}
