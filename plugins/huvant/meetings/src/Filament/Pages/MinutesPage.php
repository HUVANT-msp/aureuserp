<?php

namespace Huvant\Meetings\Filament\Pages;

use Illuminate\Contracts\Support\Htmlable;

class MinutesPage extends EmbeddedToolPage
{
    protected static ?string $slug = 'meetings';

    protected static string|\UnitEnum|null $navigationGroup = 'Meetings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Minutes';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Minutes';
    }
}
