<?php

namespace Huvant\Worklog\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

/** The entries list of My time. */
trait ListsTimeEntries
{
    use LogsTime;

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
                SelectFilter::make('user_id')->label('Person')->visible($admin)->default(fn () => (string) $this->user()->getKey())
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
                    ->action(fn (Timesheet $r, array $data) => $this->attemptEntry(
                        fn () => Worklog::updateEntry($this->user(), $r, Worklog::parse((string) $data['hours']), (string) $data['description']), 'Saved'
                    )),
                Action::make('delete')->label('Delete')->icon('heroicon-m-trash')->iconButton()->color('danger')
                    ->visible(fn (Timesheet $r): bool => Worklog::canEditEntry($this->user(), $r))
                    ->requiresConfirmation()->modalHeading('Delete this entry?')
                    ->action(fn (Timesheet $r) => $this->attemptEntry(fn () => Worklog::deleteEntry($this->user(), $r), 'Deleted')),
            ])
            ->emptyStateHeading('No time entries')
            ->emptyStateDescription('Start the timer on one of your tasks or log time by hand.')
            ->paginated([25, 50, 100]);
    }
}
