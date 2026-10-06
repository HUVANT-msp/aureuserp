<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Huvant\Documents\Filament\Concerns\BrowsesDocuments;
use Huvant\Documents\Filament\Concerns\BrowsesFolders;
use Huvant\Documents\Models\Document;
use Huvant\Documents\Support\DocumentSpace;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

/** How a product is made: procedures, drawings, photos and notes, in folders. */
class ManageRecipeDocuments extends Page implements HasTable
{
    use BrowsesDocuments, BrowsesFolders, InteractsWithRecord, InteractsWithTable {
        BrowsesDocuments::table insteadof InteractsWithTable;
    }

    protected static string $resource = RecipeResource::class;

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
        return __('huvant-orders::lab.documents');
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name.' · '.__('huvant-orders::lab.documents');
    }

    protected function documentsSpace(): DocumentSpace
    {
        return DocumentSpace::recipe((int) $this->record->getKey());
    }

    protected function documentsProjectId(): ?int
    {
        return null;
    }

    protected function documentsTaskId(): ?int
    {
        return null;
    }

    protected function documentsQuery(): Builder
    {
        return $this->documentsSpace()->scope(Document::query())->where('folder_id', $this->documentsFolderId());
    }

    protected function getHeaderActions(): array
    {
        return [$this->newFolderAction(), $this->newNoteAction(), $this->uploadAction()];
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'trail'     => $this->currentFolder()?->trail() ?? [],
            'folders'   => $this->childFolders(),
            'taskFiles' => false,
            'taskCount' => 0,
        ];
    }
}
