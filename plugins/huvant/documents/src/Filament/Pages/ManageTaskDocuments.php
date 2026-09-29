<?php

namespace Huvant\Documents\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Huvant\Documents\Filament\Concerns\BrowsesDocuments;
use Huvant\Documents\Models\Document;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Filament\Resources\TaskResource;

/** Files and notes attached to a task; they also appear in the project's documents. */
class ManageTaskDocuments extends Page implements HasTable
{
    use BrowsesDocuments, InteractsWithRecord, InteractsWithTable {
        BrowsesDocuments::table insteadof InteractsWithTable;
    }

    protected static string $resource = TaskResource::class;

    protected string $view = 'huvant-documents::filament.pages.task-documents';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-clip';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Documents';
    }

    protected function documentsProjectId(): ?int
    {
        return $this->record->project_id ? (int) $this->record->project_id : null;
    }

    protected function documentsTaskId(): ?int
    {
        return (int) $this->record->getKey();
    }

    protected function documentsFolderId(): ?int
    {
        return null;
    }

    protected function documentsQuery(): Builder
    {
        return Document::query()->where('task_id', $this->documentsTaskId());
    }

    protected function getHeaderActions(): array
    {
        return [$this->newNoteAction(), $this->uploadAction()];
    }
}
