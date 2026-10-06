<?php

namespace Huvant\Documents\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Huvant\Documents\Models\Folder;
use Huvant\Documents\Support\Documents;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

/** Folder navigation inside a documents space (a project or a recipe), on top of BrowsesDocuments. */
trait BrowsesFolders
{
    /** Folder being browsed (its id), or a page-specific virtual location. */
    #[Url(as: 'folder')]
    public ?string $location = null;

    public function openFolder(?string $location): void
    {
        $this->location = $location ?: null;
        $this->resetTable();
    }

    protected function documentsFolderId(): ?int
    {
        return ctype_digit((string) $this->location) ? (int) $this->location : null;
    }

    public function currentFolder(): ?Folder
    {
        $id = $this->documentsFolderId();

        return $id && ($space = $this->documentsSpace()) ? $space->scope(Folder::query())->find($id) : null;
    }

    /** @return Collection<int, Folder> the folders inside the current one */
    protected function childFolders(): Collection
    {
        $space = $this->documentsSpace();

        return $space ? $space->scope(Folder::query())
            ->where('parent_id', $this->currentFolder()?->getKey())
            ->withCount(['documents', 'children'])
            ->orderBy('name')
            ->get() : collect();
    }

    protected function newFolderAction(): Action
    {
        return Action::make('newFolder')
            ->label('New folder')
            ->icon('heroicon-m-folder-plus')
            ->color('gray')
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Create')
            ->schema([TextInput::make('name')->label('Name')->required()->maxLength(160)->autofocus()])
            ->action(fn (array $data) => $this->attempt(
                fn () => Documents::createFolder($this->currentUser(), $this->documentsSpace(), $this->documentsFolderId(), $data['name']),
                'Folder created',
            ));
    }

    public function renameFolderAction(): Action
    {
        return Action::make('renameFolder')
            ->label('Rename')
            ->icon('heroicon-m-pencil-square')
            ->iconButton()
            ->size('sm')
            ->color('gray')
            ->modalHeading('Rename folder')
            ->modalWidth(Width::Medium)
            ->visible(fn (array $arguments): bool => ($folder = $this->folderFromArguments($arguments)) && Documents::canManage($this->currentUser(), $folder))
            ->fillForm(fn (array $arguments): array => ['name' => $this->folderFromArguments($arguments)?->name])
            ->schema([TextInput::make('name')->label('Name')->required()->maxLength(160)])
            ->action(function (array $data, array $arguments): void {
                if ($folder = $this->folderFromArguments($arguments)) {
                    $this->attempt(fn () => Documents::renameFolder($folder, $data['name']));
                }
            });
    }

    public function deleteFolderAction(): Action
    {
        return Action::make('deleteFolder')
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->iconButton()
            ->size('sm')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => 'Delete the folder "'.($this->folderFromArguments($arguments)?->name ?? '').'"?')
            ->modalDescription('Only an empty folder can be deleted.')
            ->visible(fn (array $arguments): bool => ($folder = $this->folderFromArguments($arguments)) && Documents::canManage($this->currentUser(), $folder))
            ->action(function (array $arguments): void {
                if ($folder = $this->folderFromArguments($arguments)) {
                    $this->attempt(fn () => Documents::deleteFolder($folder), 'Folder deleted');
                }
            });
    }

    protected function folderFromArguments(array $arguments): ?Folder
    {
        $id = $arguments['folder'] ?? null;

        return $id && ($space = $this->documentsSpace()) ? $space->scope(Folder::query())->find((int) $id) : null;
    }
}
