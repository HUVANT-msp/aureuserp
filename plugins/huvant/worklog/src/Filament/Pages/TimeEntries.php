<?php

namespace Huvant\Worklog\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

/** Every declared entry: one's own, or everyone's for administrators. */
class TimeEntries extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'huvant-worklog::filament.pages.time-entries';

    protected static ?string $slug = 'worklog/entries';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return MyWeek::GROUP;
    }

    public static function getNavigationLabel(): string
    {
        return 'Time entries';
    }

    public function getTitle(): string
    {
        return 'Time entries';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add')
                ->label('Log time')
                ->icon('heroicon-m-plus')
                ->modalWidth(Width::Large)
                ->modalSubmitActionLabel('Log')
                ->schema([
                    Select::make('task_id')->label('Task')->required()->searchable()
                        ->options(fn (): array => Worklog::assignedOpenTasks($this->user())
                            ->mapWithKeys(fn ($t): array => [$t->id => $t->title.' — '.($t->project?->name ?? 'No project')])->all())
                        ->helperText('Only tasks you are assigned to.'),
                    DatePicker::make('date')->label('Day')->required()->default(now())->maxDate(now())->native(false)->displayFormat('d/m/Y'),
                    TextInput::make('from')->label('Start time (optional)')->placeholder('9:30'),
                    TextInput::make('hours')->label('Hours')->placeholder('1:30')->required(),
                    TextInput::make('description')->label('What did you do')->required()->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $this->attempt(fn () => Worklog::addEntry(
                        $this->user(), (int) $data['task_id'], (string) $data['date'], Worklog::parse((string) $data['hours']),
                        (string) $data['description'], (string) ($data['from'] ?? ''),
                    ), 'Time logged');
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $admin = Worklog::isAdmin($this->user());

        return $table
            ->query(fn (): Builder => Timesheet::query()
                ->with(['user:id,name', 'project:id,name', 'task:id,title'])
                ->when(! $admin, fn (Builder $q) => $q->where('user_id', $this->user()->getKey())))
            ->columns([
                TextColumn::make('date')->label('Day')->date('D d/m/Y')->sortable(),
                TextColumn::make('user.name')->label('Person')->visible($admin)->searchable(),
                TextColumn::make('project.name')->label('Project')->color('gray')->toggleable(),
                TextColumn::make('task.title')->label('Task')->limit(60)->tooltip(fn (Timesheet $r) => $r->task?->title)->searchable(),
                TextColumn::make('name')->label('What was done')->wrap()->searchable(),
                TextColumn::make('unit_amount')->label('Hours')->alignEnd()->sortable()
                    ->formatStateUsing(fn ($state): string => Worklog::format((float) $state))
                    ->summarize(Sum::make()->label('Total')->formatStateUsing(fn ($state): string => Worklog::format((float) $state))),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('user_id')->label('Person')->visible($admin)
                    ->options(fn (): array => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable(),
                SelectFilter::make('project_id')->label('Project')
                    ->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())->searchable(),
                Filter::make('period')->label('Period')
                    ->schema([
                        DatePicker::make('from')->label('From')->native(false)->displayFormat('d/m/Y'),
                        DatePicker::make('until')->label('Until')->native(false)->displayFormat('d/m/Y'),
                    ])
                    ->query(fn (Builder $q, array $data) => $q
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d))),
            ])
            ->recordActions([
                Action::make('edit')->label('Edit')->icon('heroicon-m-pencil-square')->iconButton()->color('gray')
                    ->visible(fn (Timesheet $r): bool => Worklog::canEditEntry($this->user(), $r))
                    ->modalWidth(Width::Large)
                    ->fillForm(fn (Timesheet $r): array => ['hours' => Worklog::format((float) $r->unit_amount), 'description' => $r->name])
                    ->schema([
                        TextInput::make('hours')->label('Hours')->required(),
                        TextInput::make('description')->label('What did you do')->required()->maxLength(255),
                    ])
                    ->action(fn (Timesheet $r, array $data) => $this->attempt(
                        fn () => Worklog::updateEntry($this->user(), $r, Worklog::parse((string) $data['hours']), (string) $data['description']), 'Saved'
                    )),
                Action::make('delete')->label('Delete')->icon('heroicon-m-trash')->iconButton()->color('danger')
                    ->visible(fn (Timesheet $r): bool => Worklog::canEditEntry($this->user(), $r))
                    ->requiresConfirmation()->modalHeading('Delete this entry?')
                    ->action(fn (Timesheet $r) => $this->attempt(fn () => Worklog::deleteEntry($this->user(), $r), 'Deleted')),
            ])
            ->emptyStateHeading('No time entries')
            ->emptyStateDescription('Start the timer on one of your tasks or log time by hand.')
            ->paginated([25, 50, 100]);
    }

    private function attempt(callable $callback, ?string $success = null): void
    {
        try {
            $callback();
            if ($success) {
                Notification::make()->success()->title($success)->send();
            }
            $this->dispatch('huvant-worklog-changed');
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }

    private function user(): User
    {
        return auth()->user();
    }
}
