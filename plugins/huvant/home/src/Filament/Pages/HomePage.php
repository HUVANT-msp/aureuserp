<?php

namespace Huvant\Home\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Home\Jobs\RefreshBriefing;
use Huvant\Home\Support\Home;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\On;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/** One's own day: what is coming up, Milo's brief, and one's tasks by urgency. */
class HomePage extends Page
{
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

    #[On('huvant-task-changed')]
    public function refreshTasks(): void {}

    protected function getViewData(): array
    {
        $user = auth()->user();

        return [
            'upcoming' => Home::upcoming($user),
            'tasks'    => Home::myTasks($user),
            'brief'    => Home::latest($user),
            'now'      => CarbonImmutable::now(),
        ];
    }
}
