<?php

namespace Huvant\Home\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Huvant\Home\Jobs\RefreshBriefing;
use Huvant\Home\Support\Home;
use Huvant\Worklog\Filament\Concerns\LogsTime;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/** One's own day: what is coming up, Milo's brief, and one's tasks by urgency. */
class HomePage extends Page
{
    use LogsTime;

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
        return [$this->logTimeAction()->color('gray')];
    }

    public function startTimer(int $taskId): void
    {
        $this->attemptEntry(fn () => Worklog::start($this->user(), $taskId), 'Timer started');
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
            'meetings' => Home::meetings($user),
            'now'      => CarbonImmutable::now(),
        ];
    }
}
