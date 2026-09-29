<?php

namespace Huvant\Tasks\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Project\Filament\Resources\ProjectResource;

/** The project's tasks as a board (a tab of the project). */
class ManageProjectBoard extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProjectResource::class;

    protected string $view = 'huvant-tasks::filament.pages.project-board';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return 'Bacheca';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }
}
