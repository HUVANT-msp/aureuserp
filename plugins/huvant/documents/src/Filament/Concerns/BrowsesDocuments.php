<?php

namespace Huvant\Documents\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Huvant\Documents\Models\Document;
use Huvant\Documents\Support\Documents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use RuntimeException;
use Webkul\Security\Models\User;

/** Documents table and actions shared by the project and the task pages. */
trait BrowsesDocuments
{
    abstract protected function documentsProjectId(): ?int;

    abstract protected function documentsTaskId(): ?int;

    abstract protected function documentsFolderId(): ?int;

    abstract protected function documentsQuery(): Builder;

    protected function currentUser(): User
    {
        return auth()->user();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => $this->documentsQuery()->with(['creator:id,name', 'editor:id,name', 'task:id,title']))
            ->columns([
                TextColumn::make('title')
                    ->label('Nome')
                    ->icon(fn (Document $record): string => $this->documentIcon($record))
                    ->iconColor(fn (Document $record): string => $record->isNote() ? 'warning' : 'primary')
                    ->weight('medium')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('task.title')
                    ->label('Task')
                    ->limit(60)
                    ->visible(fn (): bool => $this->showsTaskColumn()),
                TextColumn::make('size')
                    ->label('Dimensione')
                    ->formatStateUsing(fn (Document $record): string => $record->isNote() ? 'Nota' : Documents::humanSize($record->size))
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Autore')
                    ->color('gray'),
                TextColumn::make('updated_at')
                    ->label('Modificato')
                    ->since()
                    ->dateTimeTooltip('d/m/Y H:i')
                    ->color('gray')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordUrl(fn (Document $record): ?string => $record->isFile() ? $this->fileUrl($record) : null)
            ->openRecordUrlInNewTab()
            ->recordAction(fn (Document $record): ?string => $record->isNote() ? 'openNote' : null)
            ->recordActions([
                $this->openNoteAction(),
                Action::make('download')
                    ->label('Scarica')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->iconButton()
                    ->color('gray')
                    ->visible(fn (Document $record): bool => $record->isFile())
                    ->url(fn (Document $record): string => route('huvant.documents.show', $record)),
                Action::make('rename')
                    ->label('Rinomina')
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton()
                    ->color('gray')
                    ->visible(fn (Document $record): bool => $record->isFile() && Documents::canManage($this->currentUser(), $record))
                    ->modalWidth(Width::Medium)
                    ->fillForm(fn (Document $record): array => ['title' => $record->title])
                    ->schema([TextInput::make('title')->label('Nome')->required()->maxLength(255)])
                    ->action(function (Document $record, array $data): void {
                        $record->update(['title' => trim($data['title']), 'updated_by' => $this->currentUser()->getKey()]);
                    }),
                Action::make('move')
                    ->label('Sposta')
                    ->icon('heroicon-m-folder-arrow-down')
                    ->iconButton()
                    ->color('gray')
                    ->visible(fn (Document $record): bool => ! $record->task_id && $record->project_id && Documents::canManage($this->currentUser(), $record))
                    ->modalWidth(Width::Medium)
                    ->fillForm(fn (Document $record): array => ['folder_id' => $record->folder_id])
                    ->schema(fn (Document $record): array => [
                        Select::make('folder_id')
                            ->label('Cartella')
                            ->placeholder('Documenti del progetto (nessuna cartella)')
                            ->options(Documents::folderOptions($record->project_id))
                            ->searchable(),
                    ])
                    ->action(function (Document $record, array $data): void {
                        $this->attempt(fn () => Documents::move($record, $data['folder_id'] ? (int) $data['folder_id'] : null), 'Spostato');
                    }),
                DeleteAction::make()
                    ->iconButton()
                    ->modalHeading(fn (Document $record): string => "Eliminare «{$record->title}»?")
                    ->modalDescription('Non si può annullare.')
                    ->visible(fn (Document $record): bool => Documents::canManage($this->currentUser(), $record)),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Nessun documento')
            ->emptyStateDescription('Carica un file o scrivi una nota.')
            ->paginated([25, 50, 100]);
    }

    protected function showsTaskColumn(): bool
    {
        return false;
    }

    protected function uploadAction(): Action
    {
        return Action::make('upload')
            ->label('Carica file')
            ->icon('heroicon-m-arrow-up-tray')
            ->modalHeading('Carica file')
            ->modalSubmitActionLabel('Carica')
            ->modalWidth(Width::Large)
            ->schema([
                FileUpload::make('files')
                    ->hiddenLabel()
                    ->multiple()
                    ->required()
                    ->disk(Documents::DISK)
                    ->directory(fn (): string => Documents::directory($this->documentsProjectId()))
                    ->visibility('private')
                    ->storeFileNamesIn('names')
                    ->maxSize(Documents::MAX_UPLOAD_KB)
                    ->maxParallelUploads(3)
                    ->panelLayout('grid')
                    ->helperText('Fino a 100 MB per file.'),
            ])
            ->action(function (array $data): void {
                $user = $this->currentUser();
                $names = $data['names'] ?? [];
                foreach ((array) $data['files'] as $path) {
                    Documents::registerStoredFile(
                        $user, $this->documentsProjectId(), $this->documentsFolderId(), $this->documentsTaskId(), $path, $names[$path] ?? null
                    );
                }
                Notification::make()->success()->title(count((array) $data['files']) === 1 ? 'File caricato' : 'File caricati')->send();
            });
    }

    protected function newNoteAction(): Action
    {
        return Action::make('newNote')
            ->label('Nuova nota')
            ->icon('heroicon-m-pencil')
            ->color('gray')
            ->modalHeading('Nuova nota')
            ->modalSubmitActionLabel('Salva')
            ->modalWidth(Width::FourExtraLarge)
            ->schema($this->noteSchema())
            ->action(function (array $data): void {
                Documents::createNote(
                    $this->currentUser(), $this->documentsProjectId(), $this->documentsFolderId(), $this->documentsTaskId(), $data['title'], $data['body'] ?? null
                );
                Notification::make()->success()->title('Nota salvata')->send();
            });
    }

    protected function openNoteAction(): Action
    {
        return Action::make('openNote')
            ->label('Apri')
            ->icon('heroicon-m-eye')
            ->iconButton()
            ->color('gray')
            ->visible(fn (Document $record): bool => $record->isNote())
            ->modalHeading(fn (Document $record): string => $record->title)
            ->modalDescription(fn (Document $record): string => 'Ultima modifica di '.($record->editor?->name ?? $record->creator?->name ?? '—').' · '.$record->updated_at?->format('d/m/Y H:i'))
            ->modalWidth(Width::FourExtraLarge)
            ->modalContent(fn (Document $record): HtmlString => new HtmlString(
                '<div class="hv-doc-note fi-prose">'.(filled($record->body) ? str($record->body)->sanitizeHtml() : '<p class="hv-doc-muted">Nota vuota.</p>').'</div>'
            ))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Chiudi')
            ->extraModalFooterActions(fn (Action $action): array => [
                $action->makeModalSubmitAction('edit', ['edit' => true])->label('Modifica')->icon('heroicon-m-pencil-square')->color('primary'),
            ])
            ->action(function (Document $record, array $arguments): void {
                if ($arguments['edit'] ?? false) {
                    $this->replaceMountedAction('editNote', ['record' => $record->getKey()]);
                }
            });
    }

    public function editNoteAction(): Action
    {
        return Action::make('editNote')
            ->modalHeading('Modifica nota')
            ->modalSubmitActionLabel('Salva')
            ->modalWidth(Width::FourExtraLarge)
            ->fillForm(fn (array $arguments): array => $this->findDocument($arguments['record'] ?? null)?->only(['title', 'body']) ?? [])
            ->schema($this->noteSchema())
            ->action(function (array $data, array $arguments): void {
                $document = $this->findDocument($arguments['record'] ?? null);
                if (! $document?->isNote()) {
                    return;
                }
                $document->update(['title' => trim($data['title']), 'body' => $data['body'] ?? null, 'updated_by' => $this->currentUser()->getKey()]);
                Notification::make()->success()->title('Nota salvata')->send();
            });
    }

    protected function noteSchema(): array
    {
        return [
            TextInput::make('title')->label('Titolo')->required()->maxLength(255)->autofocus(),
            RichEditor::make('body')
                ->hiddenLabel()
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'link'],
                    ['h2', 'h3'],
                    ['bulletList', 'orderedList', 'blockquote', 'codeBlock'],
                    ['table'],
                    ['undo', 'redo'],
                ])
                ->extraInputAttributes(['style' => 'min-height: 16rem']),
        ];
    }

    protected function findDocument(mixed $id): ?Document
    {
        return $id ? $this->documentsQueryForLookup()->whereKey((int) $id)->first() : null;
    }

    /** Lookups stay inside what this page shows (its project or task). */
    protected function documentsQueryForLookup(): Builder
    {
        return Document::query()
            ->when($this->documentsTaskId(), fn ($q, $taskId) => $q->where('task_id', $taskId), fn ($q) => $q->where('project_id', $this->documentsProjectId()));
    }

    protected function fileUrl(Document $document): string
    {
        return route('huvant.documents.show', ['document' => $document, 'inline' => $document->previewable() ? 1 : null]);
    }

    protected function documentIcon(Document $document): string
    {
        if ($document->isNote()) {
            return 'heroicon-o-pencil-square';
        }
        $mime = (string) $document->mime_type;

        return match (true) {
            str_starts_with($mime, 'image/')                                                          => 'heroicon-o-photo',
            str_starts_with($mime, 'video/')                                                          => 'heroicon-o-film',
            str_starts_with($mime, 'audio/')                                                          => 'heroicon-o-musical-note',
            $mime === 'application/pdf'                                                               => 'heroicon-o-document-text',
            str_contains($mime, 'sheet') || str_contains($mime, 'excel') || $mime === 'text/csv'      => 'heroicon-o-table-cells',
            str_contains($mime, 'presentation') || str_contains($mime, 'powerpoint')                  => 'heroicon-o-presentation-chart-bar',
            str_contains($mime, 'zip') || str_contains($mime, 'compressed')                           => 'heroicon-o-archive-box',
            default                                                                                   => 'heroicon-o-document',
        };
    }

    protected function attempt(callable $callback, ?string $success = null): bool
    {
        try {
            $callback();
            if ($success) {
                Notification::make()->success()->title($success)->send();
            }

            return true;
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return false;
        }
    }
}
