<?php

namespace Huvant\Calendar\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Calendar\Filament\Concerns\EditsEvents;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Livewire\Attributes\Url;
use RuntimeException;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

/** Who is where, who can be reached now, and the meetings of the week. */
class CalendarPage extends Page
{
    use EditsEvents;

    public const GROUP = 'Calendar';

    protected string $view = 'huvant-calendar::filament.pages.calendar';

    protected static ?string $slug = 'calendar';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    #[Url(as: 'view')]
    public string $tab = 'presence';

    #[Url(as: 'week')]
    public string $week = '';

    #[Url(as: 'who')]
    public string $scope = 'me';

    #[Url(as: 'team')]
    public ?int $team = null;

    public bool $weekend = false;

    #[Url(as: 'event')]
    public ?int $eventId = null;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return self::GROUP;
    }

    public static function getNavigationLabel(): string
    {
        return 'Company calendar';
    }

    public function getTitle(): string
    {
        return 'Company calendar';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
        $this->tab = in_array($this->tab, ['presence', 'agenda'], true) ? $this->tab : 'presence';
        if ($this->eventId) {
            $this->openEvent($this->eventId);
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['presence', 'agenda'], true) ? $tab : 'presence';
    }

    public function shiftWeek(int $weeks): void
    {
        $this->week = $this->monday()->addWeeks($weeks)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->week = CarbonImmutable::today()->startOfWeek()->toDateString();
    }

    public function showPerson(int $userId): void
    {
        $this->scope = (string) $userId;
        $this->tab = 'agenda';
    }

    public function openEvent(int $id): void
    {
        if (! Event::query()->whereKey($id)->exists()) {
            $this->eventId = null;

            return;
        }
        $this->eventId = $id;
        $this->dispatch('open-modal', id: 'hv-cal-event');
    }

    public function closeEvent(): void
    {
        $this->eventId = null;
    }

    public function respond(string $response): void
    {
        $event = $this->eventId ? Event::query()->find($this->eventId) : null;
        if ($event) {
            $this->attempt(fn () => Calendar::respond($this->user(), $event, $response), 'Reply sent');
        }
    }

    public function deleteEvent(): void
    {
        $event = $this->eventId ? Event::query()->find($this->eventId) : null;
        if ($event && $this->attempt(fn () => Calendar::delete($this->user(), $event), 'Event deleted')) {
            $this->eventId = null;
            $this->dispatch('close-modal', id: 'hv-cal-event');
        }
    }

    /** From one's own row of "Who's where": where I am on that day. */
    public function setMyDay(string $date, string $kind): void
    {
        $this->attempt(fn () => Calendar::setPresence($this->user(), CarbonImmutable::parse($date), $kind),
            ($kind === 'office' ? 'Office' : Calendar::kind($kind)[0]).' on '.CarbonImmutable::parse($date)->format('D j M'));
    }

    /** One click: "I'm working remotely today", "I'm out of office today". */
    public function markToday(string $kind): void
    {
        $this->attempt(fn () => Calendar::save($this->user(), [
            'kind' => $kind, 'date' => CarbonImmutable::today()->toDateString(), 'all_day' => true, 'attendees' => [$this->user()->getKey()],
        ]), Calendar::kind($kind)[0].' today: saved');
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('todayRemote')->label('Working remotely today')->icon('heroicon-m-home')->action(fn () => $this->markToday('remote')),
                Action::make('todayTravel')->label('Travelling today')->icon('heroicon-m-paper-airplane')->action(fn () => $this->markToday('travel')),
                Action::make('todayAway')->label('Out of office today')->icon('heroicon-m-no-symbol')->action(fn () => $this->markToday('away')),
            ])->label('My day')->icon('heroicon-m-map-pin')->button()->color('gray'),
            $this->eventAction('create')->label('New event')->icon('heroicon-m-plus'),
        ];
    }

    public function editEventAction(): Action
    {
        return $this->eventAction('editEvent')->label('Edit')->icon('heroicon-m-pencil-square')->color('gray')
            ->fillForm(function (): array {
                $e = Event::query()->with('attendees')->findOrFail($this->eventId);

                return [
                    'kind'        => $e->kind,
                    'title'       => $e->title,
                    'date'        => $e->starts_at->toDateString(),
                    'date_to'     => ($e->all_day ? $e->ends_at->subDay() : $e->ends_at)->toDateString(),
                    'all_day'     => $e->all_day,
                    'from'        => $e->starts_at->format('H:i'),
                    'to'          => $e->ends_at->format('H:i'),
                    'attendees'   => $e->attendees->pluck('user_id')->reject(fn ($id) => (int) $id === (int) $e->organizer_id)->map(fn ($id) => (string) $id)->values()->all(),
                    'location'    => $e->location,
                    'description' => $e->description,
                    'project_id'  => $e->project_id,
                    'private'     => $e->private,
                ];
            })
            ->visible(fn (): bool => ($e = $this->eventId ? Event::query()->find($this->eventId) : null) && Calendar::canEdit($this->user(), $e));
    }

    protected function calendarUser(): User
    {
        return $this->user();
    }

    protected function editedEventId(): ?int
    {
        return $this->eventId;
    }

    protected function onEventSaved(Event $event): void
    {
        $this->week = $event->starts_at->startOfWeek()->toDateString();
    }

    protected function getViewData(): array
    {
        $monday = $this->monday();
        $user = $this->user();
        $days = $this->weekend ? 7 : 5;
        $event = $this->eventId ? Event::query()->with(['attendees.user:id,name', 'organizer:id,name', 'project:id,name'])->find($this->eventId) : null;

        return [
            'monday'    => $monday,
            'days'      => $days,
            'today'     => CarbonImmutable::today(),
            'teams'     => Team::query()->orderBy('name')->get(['id', 'name']),
            'people'    => Calendar::people(),
            'board'     => $this->tab === 'presence' ? Calendar::board(Calendar::people($this->team), $monday, $days) : null,
            'agenda'    => $this->tab === 'agenda' ? Calendar::agenda($user, $monday, $this->scope, $days) : null,
            'pending'   => Calendar::pendingFor($user),
            'event'     => $event,
            'eventView' => $event ? Calendar::view($event, $user) : null,
            'canEdit'   => $event ? Calendar::canEdit($user, $event) : false,
        ];
    }

    private function attempt(callable $callback, ?string $success = null): bool
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
