<?php

namespace Huvant\Documents\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Huvant\Documents\Filament\Concerns\BrowsesDocuments;
use Huvant\Documents\Models\Document;
use Huvant\Documents\Models\Folder;
use Huvant\Documents\Support\Documents;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Webkul\Project\Filament\Resources\ProjectResource;

/** A project's documents: folders, files and notes, for everyone in the project's teams. */
class ManageProjectDocuments extends Page implements HasTable
{
    use BrowsesDocuments, InteractsWithRecord, InteractsWithTable {
        BrowsesDocuments::table insteadof InteractsWithTable;
    }

    protected static string $resource = ProjectResource::class;

    protected string $view = 'huvant-documents::filament.pages.project-documents';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    /** Folder being browsed; "task" shows the files attached to the project's tasks. */
    #[Url(as: 'cartella')]
    public ?string $location = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        if ($this->documentsFolderId() && ! $this->currentFolder()) {
            $this->location = null;
        }
    }

    public static function getNavigationLabel(): string
    {
        return 'Documenti';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Documenti';
    }

    public function openFolder(?string $location): void
    {
        $this->location = $location ?: null;
        $this->resetTable();
    }

    protected function documentsProjectId(): ?int
    {
        return (int) $this->record->getKey();
    }

    protected function documentsTaskId(): ?int
    {
        return null;
    }

    protected function documentsFolderId(): ?int
    {
        return ctype_digit((string) $this->location) ? (int) $this->location : null;
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

    public function currentFolder(): ?Folder
    {
        $id = $this->documentsFolderId();

        return $id ? Folder::query()->where('project_id', $this->documentsProjectId())->find($id) : null;
    }

    protected function getHeaderActions(): array
    {
        if ($this->showingTaskFiles()) {
            return [];
        }

        return [
            Action::make('newFolder')
                ->label('Nuova cartella')
                ->icon('heroicon-m-folder-plus')
                ->color('gray')
                ->modalWidth(Width::Medium)
                ->modalSubmitActionLabel('Crea')
                ->schema([TextInput::make('name')->label('Nome')->required()->maxLength(160)->autofocus()])
                ->action(fn (array $data) => $this->attempt(
                    fn () => Documents::createFolder($this->currentUser(), $this->documentsProjectId(), $this->documentsFolderId(), $data['name']),
                    'Cartella creata',
                )),
            $this->newNoteAction(),
            $this->uploadAction(),
        ];
    }

    public function renameFolderAction(): Action
    {
        return Action::make('renameFolder')
            ->label('Rinomina')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->size('sm')
            ->color('gray')
            ->modalHeading('Rinomina cartella')
            ->modalWidth(Width::Medium)
            ->visible(fn (array $arguments): bool => ($folder = $this->folderFromArguments($arguments)) && Documents::canManage($this->currentUser(), $folder))
            ->fillForm(fn (array $arguments): array => ['name' => $this->folderFromArguments($arguments)?->name])
            ->schema([TextInput::make('name')->label('Nome')->required()->maxLength(160)])
            ->action(function (array $data, array $arguments): void {
                if ($folder = $this->folderFromArguments($arguments)) {
                    $this->attempt(fn () => Documents::renameFolder($folder, $data['name']));
                }
            });
    }

    public function deleteFolderAction(): Action
    {
        return Action::make('deleteFolder')
            ->label('Elimina')
            ->icon('heroicon-m-trash')
            ->iconButton()
            ->size('sm')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => 'Eliminare la cartella «'.($this->folderFromArguments($arguments)?->name ?? '').'»?')
            ->modalDescription('Si può eliminare solo una cartella vuota.')
            ->visible(fn (array $arguments): bool => ($folder = $this->folderFromArguments($arguments)) && Documents::canManage($this->currentUser(), $folder))
            ->action(function (array $arguments): void {
                if ($folder = $this->folderFromArguments($arguments)) {
                    $this->attempt(fn () => Documents::deleteFolder($folder), 'Cartella eliminata');
                }
            });
    }

    protected function folderFromArguments(array $arguments): ?Folder
    {
        $id = $arguments['folder'] ?? null;

        return $id ? Folder::query()->where('project_id', $this->documentsProjectId())->find((int) $id) : null;
    }

    protected function getViewData(): array
    {
        $current = $this->currentFolder();
        $folders = $this->showingTaskFiles() ? collect() : Folder::query()
            ->where('project_id', $this->documentsProjectId())
            ->where('parent_id', $current?->getKey())
            ->withCount(['documents', 'children'])
            ->orderBy('name')
            ->get();

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
