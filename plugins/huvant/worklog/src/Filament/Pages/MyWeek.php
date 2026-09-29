<?php

namespace Huvant\Worklog\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Worklog\Support\Worklog;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use RuntimeException;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/**
 * The person's week: only the tasks they are assigned to. To log time on
 * another task they join it first, as co-assignee.
 */
class MyWeek extends Page
{
    protected string $view = 'huvant-worklog::filament.pages.my-week';

    protected static ?string $slug = 'worklog/week';

    protected static ?int $navigationSort = 80;

    #[Url(as: 'settimana')]
    public string $week = '';

    public string $search = '';

    public bool $joining = false;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return 'La mia settimana';
    }

    public function getTitle(): string
    {
        return 'La mia settimana';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
    }

    public function shiftWeek(int $weeks): void
    {
        $this->week = $this->monday()->addWeeks($weeks)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->week = CarbonImmutable::today()->startOfWeek()->toDateString();
    }

    public function saveCell(int $taskId, string $date, string $value): void
    {
        try {
            Worklog::setDayTotal($this->user(), $taskId, $date, Worklog::parse($value));
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
        $this->dispatch('huvant-worklog-changed');
    }

    public function start(int $taskId): void
    {
        try {
            Worklog::start($this->user(), $taskId);
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
        $this->dispatch('huvant-worklog-changed');
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
            'monday'   => $monday,
            'grid'     => Worklog::week($user, $monday),
            'running'  => Worklog::running($user),
            'joinable' => $this->joining ? Worklog::joinableTasks($user, trim($this->search)) : collect(),
            'today'    => CarbonImmutable::today()->toDateString(),
        ];
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
