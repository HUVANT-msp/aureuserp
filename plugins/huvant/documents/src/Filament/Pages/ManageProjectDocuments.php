<?php

namespace Huvant\Documents\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Huvant\Documents\Filament\Concerns\BrowsesDocuments;
use Huvant\Documents\Filament\Concerns\BrowsesFolders;
use Huvant\Documents\Models\Document;
use Huvant\Documents\Support\Documents;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Filament\Resources\ProjectResource;

/** A project's documents: folders, files and notes, for everyone in the project's teams. */
class ManageProjectDocuments extends Page implements HasTable
{
    use BrowsesDocuments, BrowsesFolders, InteractsWithRecord, InteractsWithTable {
        BrowsesDocuments::table insteadof InteractsWithTable;
    }

    protected static string $resource = ProjectResource::class;

    protected string $view = 'huvant-documents::filament.pages.project-documents';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        if ($this->documentsFolderId() && ! $this->currentFolder()) {
            $this->location = null;
        }
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'it' ? 'Documenti' : 'Documents';
    }

    public function getTitle(): string|Htmlable
    {
        return app()->getLocale() === 'it' ? 'Documenti' : 'Documents';
    }

    protected function documentsProjectId(): ?int
    {
        return (int) $this->record->getKey();
    }

    protected function documentsTaskId(): ?int
    {
        return null;
    }

    protected function showingTaskFiles(): bool
    {
        return $this->location === 'task';
    }

    protected function showsTaskColumn(): bool
    {
        return $this->showingTaskFiles();
    }

    protected function documentsQuery(): Builder
    {
        return Document::query()
            ->where('project_id', $this->documentsProjectId())
            ->when(
                $this->showingTaskFiles(),
                fn ($q) => $q->whereNotNull('task_id'),
                fn ($q) => $q->whereNull('task_id')->where('folder_id', $this->documentsFolderId()),
            );
    }

    protected function getHeaderActions(): array
    {
        if ($this->showingTaskFiles()) {
            return [];
        }

        return [
            $this->newFolderAction(),
            $this->newNoteAction(),
            $this->uploadAction(),
        ];
    }

    protected function getViewData(): array
    {
        $current = $this->currentFolder();
        $folders = $this->showingTaskFiles() ? collect() : $this->childFolders();

        return [
            'trail'     => $current?->trail() ?? [],
            'folders'   => $folders,
            'taskFiles' => $this->showingTaskFiles(),
            'taskCount' => ! $current && ! $this->showingTaskFiles()
                ? Document::query()->where('project_id', $this->documentsProjectId())->whereNotNull('task_id')->count()
                : 0,
        ];
    }
}
