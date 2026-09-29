<?php

namespace Huvant\Meetings\Filament\Pages;

class Minutes extends EmbeddedMeetingsPage
{
    protected static ?string $slug = 'riunioni/verbali';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return 'Verbali';
    }

    public function getTitle(): string
    {
        return 'Verbali';
    }

    protected function target(): string
    {
        return '/';
    }
}
