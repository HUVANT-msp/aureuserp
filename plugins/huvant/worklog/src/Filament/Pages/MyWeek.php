<?php

namespace Huvant\Worklog\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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
class MyWeek extends Page
{
    public const GROUP = 'Ore';

    protected string $view = 'huvant-worklog::filament.pages.my-week';

    protected static ?string $slug = 'worklog/week';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    #[Url(as: 'settimana')]
    public string $week = '';

    #[Url(as: 'vista')]
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
        return 'Le mie ore';
    }

    public function getTitle(): string
    {
        return 'Le mie ore';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
        $this->tab = in_array($this->tab, ['week', 'timeline'], true) ? $this->tab : 'week';
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['week', 'timeline'], true) ? $tab : 'week';
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
        ), 'Ore registrate');
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
        ), 'Salvato');
    }

    public function deleteEntry(int $id): void
    {
        $entry = Timesheet::query()->find($id);
        if ($entry && $this->attempt(fn () => Worklog::deleteEntry($this->user(), $entry), 'Eliminato')) {
            $this->openDay((int) $this->cellTask, $this->cellDate);
        }
    }

    public function start(int $taskId): void
    {
        $this->attempt(fn () => Worklog::start($this->user(), $taskId), 'Timer avviato');
    }

    public function join(int $taskId): void
    {
        Worklog::join($this->user(), $taskId);
        $this->search = '';
        $this->joining = false;
        Notification::make()->success()->title('Ora sei assegnatario del task')->send();
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
