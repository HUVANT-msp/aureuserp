<?php

namespace Huvant\Worklog\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Huvant\Worklog\Filament\Concerns\ListsTimeEntries;
use Huvant\Worklog\Support\Worklog;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use RuntimeException;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

/**
 * The person's hours: the week grid (tasks they are assigned to) and the
 * timeline of their days. Every entry says what was done.
 */
class MyWeek extends Page implements HasTable
{
    use InteractsWithTable, ListsTimeEntries {
        ListsTimeEntries::table insteadof InteractsWithTable;
    }

    public const GROUP = 'Time';

    protected string $view = 'huvant-worklog::filament.pages.my-week';

    protected static ?string $slug = 'worklog/week';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    #[Url(as: 'week')]
    public string $week = '';

    #[Url(as: 'view')]
    public string $tab = 'week';

    public string $search = '';

    public bool $joining = false;

    /** The day being edited in the entries dialog. */
    public ?int $cellTask = null;

    public string $cellDate = '';

    public array $entry = ['from' => '', 'hours' => '', 'description' => ''];

    public array $edits = [];

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return self::GROUP;
    }

    public static function getNavigationLabel(): string
    {
        return 'My time';
    }

    public function getTitle(): string
    {
        return 'My time';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
        $this->tab = in_array($this->tab, ['week', 'timeline', 'list'], true) ? $this->tab : 'week';
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['week', 'timeline', 'list'], true) ? $tab : 'week';
    }

    protected function getHeaderActions(): array
    {
        return [$this->logTimeAction()];
    }

    /** The entry being edited from the timeline. */
    public array $edit = ['id' => null, 'date' => '', 'from' => '', 'hours' => '', 'description' => ''];

    public function editEntry(int $id): void
    {
        $entry = Timesheet::query()->where('user_id', $this->user()->getKey())->find($id);
        if (! $entry) {
            return;
        }
        $this->edit = ['id' => $id, ...Worklog::entryForm($entry)];
        $this->edit['from'] ??= '';
        $this->dispatch('open-modal', id: 'hv-entry-edit');
    }

    public function saveEdit(): void
    {
        $entry = Timesheet::query()->find((int) $this->edit['id']);
        if ($entry && $this->attempt(fn () => Worklog::reschedule(
            $this->user(), $entry, (string) $this->edit['date'], (string) $this->edit['from'] ?: null,
            Worklog::parse((string) $this->edit['hours']), (string) $this->edit['description'],
        ), 'Saved')) {
            $this->dispatch('close-modal', id: 'hv-entry-edit');
        }
    }

    public function deleteEdited(): void
    {
        $entry = Timesheet::query()->find((int) $this->edit['id']);
        if ($entry && $this->attempt(fn () => Worklog::deleteEntry($this->user(), $entry), 'Deleted')) {
            $this->dispatch('close-modal', id: 'hv-entry-edit');
        }
    }

    /** Drag on the timeline: new day and start (minutes from midnight), same length. */
    public function moveBlock(int $id, string $date, int $fromMinutes): void
    {
        $entry = Timesheet::query()->find($id);
        if (! $entry) {
            return;
        }
        $form = Worklog::entryForm($entry);
        $fromMinutes = max(0, min(24 * 60 - 5, intdiv($fromMinutes, 5) * 5));
        $this->attempt(fn () => Worklog::reschedule(
            $this->user(), $entry, $date, sprintf('%d:%02d', intdiv($fromMinutes, 60), $fromMinutes % 60), (float) $entry->unit_amount, $form['description'],
        ));
    }

    /** Resize on the timeline: new length in minutes. */
    public function resizeBlock(int $id, int $minutes): void
    {
        $entry = Timesheet::query()->find($id);
        if (! $entry) {
            return;
        }
        $form = Worklog::entryForm($entry);
        $this->attempt(fn () => Worklog::reschedule(
            $this->user(), $entry, $form['date'], $form['from'], max(5, $minutes) / 60, $form['description'],
        ));
    }

    public function shiftWeek(int $weeks): void
    {
        $this->week = $this->monday()->addWeeks($weeks)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->week = CarbonImmutable::today()->startOfWeek()->toDateString();
    }

    public function openDay(int $taskId, string $date): void
    {
        $this->cellTask = $taskId;
        $this->cellDate = CarbonImmutable::parse($date)->toDateString();
        $this->entry = ['from' => '', 'hours' => '', 'description' => ''];
        $this->edits = Worklog::entriesOn($this->user(), $taskId, $this->cellDate)
            ->mapWithKeys(fn (Timesheet $t): array => [$t->id => ['hours' => Worklog::format((float) $t->unit_amount), 'description' => (string) $t->name]])
            ->all();
        $this->dispatch('open-modal', id: 'hv-day-entries');
    }

    public function addEntry(): void
    {
        $ok = $this->attempt(fn () => Worklog::addEntry(
            $this->user(), (int) $this->cellTask, $this->cellDate, Worklog::parse((string) $this->entry['hours']),
            (string) $this->entry['description'], (string) $this->entry['from'],
        ), 'Time logged');
        if ($ok) {
            $this->openDay((int) $this->cellTask, $this->cellDate);
        }
    }

    public function saveEntry(int $id): void
    {
        $entry = Timesheet::query()->find($id);
        if (! $entry) {
            return;
        }
        $this->attempt(fn () => Worklog::updateEntry(
            $this->user(), $entry, Worklog::parse((string) ($this->edits[$id]['hours'] ?? '')), (string) ($this->edits[$id]['description'] ?? '')
        ), 'Saved');
    }

    public function deleteEntry(int $id): void
    {
        $entry = Timesheet::query()->find($id);
        if ($entry && $this->attempt(fn () => Worklog::deleteEntry($this->user(), $entry), 'Deleted')) {
            $this->openDay((int) $this->cellTask, $this->cellDate);
        }
    }

    public function start(int $taskId): void
    {
        $this->attempt(fn () => Worklog::start($this->user(), $taskId), 'Timer started');
    }

    public function join(int $taskId): void
    {
        Worklog::join($this->user(), $taskId);
        $this->search = '';
        $this->joining = false;
        Notification::make()->success()->title('You are now assigned to the task')->send();
        $this->dispatch('huvant-worklog-changed');
    }

    #[On('huvant-worklog-changed')]
    public function refreshWeek(): void {}

    protected function getViewData(): array
    {
        $monday = $this->monday();
        $user = $this->user();

        return [
            'monday'    => $monday,
            'grid'      => $this->tab === 'week' ? Worklog::week($user, $monday) : null,
            'timeline'  => $this->tab === 'timeline' ? Worklog::timeline($user, $monday) : null,
            'running'   => Worklog::running($user),
            'joinable'  => $this->joining ? Worklog::joinableTasks($user, trim($this->search)) : collect(),
            'today'     => CarbonImmutable::today()->toDateString(),
            'cellTitle' => $this->cellTask ? Task::query()->whereKey($this->cellTask)->value('title') : null,
        ];
    }

    private function attempt(callable $callback, ?string $success = null): bool
    {
        try {
            $callback();
            if ($success) {
                Notification::make()->success()->title($success)->send();
            }
            $this->dispatch('huvant-worklog-changed');

            return true;
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return false;
        }
    }

    private function monday(): CarbonImmutable
    {
        try {
            $day = $this->week !== '' ? CarbonImmutable::parse($this->week) : CarbonImmutable::today();
        } catch (\Throwable) {
            $day = CarbonImmutable::today();
        }

        return $day->startOfWeek();
    }

    private function user(): User
    {
        return auth()->user();
    }
}
