<?php

namespace Huvant\Home\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Calendar\Filament\Concerns\EditsEvents;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Huvant\Home\Jobs\RefreshBriefing;
use Huvant\Home\Support\Home;
use Huvant\Worklog\Filament\Concerns\LogsTime;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use RuntimeException;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/** One's own day: what is coming up, Milo's brief, and one's tasks by urgency. */
class HomePage extends Page
{
    use EditsEvents, LogsTime;

    public string $stopNote = '';

    protected string $view = 'huvant-home::filament.pages.home';

    protected static ?string $slug = 'home';

    protected static ?int $navigationSort = -10;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationLabel(): string
    {
        return 'My day';
    }

    public function getTitle(): string
    {
        $hour = (int) now()->format('G');
        $part = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        return $part.', '.strtok((string) auth()->user()?->name, ' ');
    }

    public function getSubheading(): ?string
    {
        return now()->format('l j F');
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function refreshBrief(): void
    {
        $key = 'milo-brief:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 1)) {
            Notification::make()->warning()->title('Milo can write a new brief in '.ceil(RateLimiter::availableIn($key) / 60).' min')->send();

            return;
        }
        RateLimiter::hit($key, 15 * 60);
        RefreshBriefing::dispatch((int) auth()->id());
        Notification::make()->title('Milo is writing your brief…')->body('It will appear here in a minute.')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->eventAction('newEvent')->label('New event')->icon('heroicon-m-calendar-days')->color('gray'),
            $this->logTimeAction()->color('gray'),
        ];
    }

    public function startTimer(int $taskId): void
    {
        $this->attemptEntry(fn () => Worklog::start($this->user(), $taskId), 'Timer started');
    }

    public function stopTimer(): void
    {
        try {
            $entry = Worklog::stop($this->user(), $this->stopNote);
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();

            return; // the dialog stays open
        }
        Notification::make()->success()->title($entry ? 'Logged '.Worklog::format((float) $entry->unit_amount).' h' : 'Timer stopped (under a minute, nothing logged)')->send();
        $this->stopNote = '';
        $this->dispatch('close-modal', id: 'hv-home-stop');
        $this->dispatch('huvant-worklog-changed');
    }

    public function discardTimer(): void
    {
        Worklog::discard($this->user());
        $this->dispatch('close-modal', id: 'hv-home-stop');
        $this->dispatch('huvant-worklog-changed');
    }

    protected function calendarUser(): User
    {
        return $this->user();
    }

    public function respondInvite(int $eventId, string $response): void
    {
        $event = Event::query()->find($eventId);
        if ($event) {
            $this->attemptEntry(fn () => Calendar::respond($this->user(), $event, $response), 'Reply sent');
        }
    }

    #[On('huvant-task-changed')]
    #[On('huvant-worklog-changed')]
    public function refreshTasks(): void {}

    protected function user(): User
    {
        return auth()->user();
    }

    protected function getViewData(): array
    {
        $user = auth()->user();

        return [
            'upcoming' => Home::upcoming($user),
            'tasks'    => Home::myTasks($user),
            'brief'    => Home::latest($user),
            'today'    => Home::today($user),
            'waiting'  => Home::waiting($user),
            'now'      => CarbonImmutable::now(),
        ];
    }
}
