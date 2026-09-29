<?php

namespace Huvant\Worklog\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Huvant\Worklog\Support\Worklog;
use RuntimeException;

/** "Log time": the same action wherever time can be logged (My time, the home page). */
trait LogsTime
{
    public function logTimeAction(): Action
    {
        return Action::make('logTime')
            ->label('Log time')->modalHeading('Log time')
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
                $this->attemptEntry(fn () => Worklog::addEntry(
                    $this->user(), (int) $data['task_id'], (string) $data['date'], Worklog::parse((string) $data['hours']),
                    (string) $data['description'], (string) ($data['from'] ?? ''),
                ), 'Time logged');
            });
    }

    protected function attemptEntry(callable $callback, ?string $success = null): void
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
}
