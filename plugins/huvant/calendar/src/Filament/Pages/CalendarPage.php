<?php

namespace Huvant\Calendar\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Huvant\Calendar\Filament\Concerns\EditsEvents;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Huvant\Calendar\Support\SmartWorking;
use Livewire\Attributes\Url;
use RuntimeException;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

/** Who is where, who can be reached now, and the meetings of the week. */
class CalendarPage extends Page
{
    use EditsEvents;

    public const GROUP = 'Calendar';

    /** The "Calendar" and "Who's where" tabs show this many weeks at once. */
    public const WEEKS = 2;

    protected string $view = 'huvant-calendar::filament.pages.calendar';

    protected static ?string $slug = 'calendar';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    #[Url(as: 'view')]
    public string $tab = 'calendar';

    #[Url(as: 'week')]
    public string $week = '';

    #[Url(as: 'day')]
    public string $day = '';

    #[Url(as: 'who')]
    public string $scope = 'me';

    #[Url(as: 'team')]
    public ?int $team = null;

    public bool $weekend = false;

    #[Url(as: 'event')]
    public ?int $eventId = null;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return self::GROUP; // the key: CalendarPlugin gives it its label and launcher icon
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'it' ? 'Calendario aziendale' : 'Company calendar';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'it' ? 'Calendario aziendale' : 'Company calendar';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
        $this->day = $this->selectedDay()->toDateString();
        $this->tab = in_array($this->tab, ['calendar', 'day', 'presence'], true) ? $this->tab : 'calendar';
        if ($this->eventId) {
            $this->openEvent($this->eventId);
        }
    }

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['calendar', 'day', 'presence'], true) ? $tab : 'calendar';
    }

    public function shiftWeek(int $weeks): void
    {
        $this->week = $this->monday()->addWeeks($weeks)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->week = CarbonImmutable::today()->startOfWeek()->toDateString();
    }

    public function shiftDay(int $days): void
    {
        $this->day = $this->selectedDay()->addDays($days)->toDateString();
    }

    public function todayDay(): void
    {
        $this->day = CarbonImmutable::today()->toDateString();
    }

    public function showPerson(int $userId): void
    {
        $this->scope = (string) $userId;
        $this->tab = 'calendar';
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
                Action::make('todayTravel')->label('Travelling today')->icon('heroicon-m-paper-airplane')->action(fn () => $this->markToday('travel')),
                Action::make('todayAway')->label('Out of office today')->icon('heroicon-m-no-symbol')->action(fn () => $this->markToday('away')),
            ])->label('My day')->icon('heroicon-m-map-pin')->button()->color('gray'),
            $this->smartWorkingReferentAction(),
            $this->smartWorkingRequestAction(),
            $this->eventAction('create')->label('New event')->icon('heroicon-m-plus'),
        ];
    }

    /** Ask the referent for a smart working day; less than a week's notice is flagged but allowed. */
    public function smartWorkingRequestAction(): Action
    {
        $isIt = app()->getLocale() === 'it';

        return Action::make('smartWorkingRequest')
            ->label($isIt ? 'Nuova richiesta di smart working' : 'New smart working request')
            ->icon('heroicon-m-home')
            ->color('gray')
            ->modalHeading($isIt ? 'Nuova richiesta di smart working' : 'New smart working request')
            ->modalDescription(fn (): string => ($referent = SmartWorking::referent())
                ? ($isIt ? "Milo manda la richiesta a {$referent->name}. Risulterai in smart working nel calendario solo dopo l'approvazione." : "Milo sends the request to {$referent->name}. You show as remote in the calendar only once it is approved.")
                : ($isIt ? 'Nessun referente per lo smart working: chiedi a un admin di impostarlo.' : 'No smart working referent yet: ask an admin to set one.'))
            ->modalSubmitActionLabel($isIt ? 'Conferma' : 'Confirm')
            ->fillForm(fn (array $arguments): array => ['day' => $arguments['day'] ?? CarbonImmutable::today()->addWeek()->toDateString()])
            ->schema([
                DatePicker::make('day')
                    ->label($isIt ? 'Giorno' : 'Day')
                    ->native(false)->displayFormat('D d/m/Y')->firstDayOfWeek(1)
                    ->minDate(CarbonImmutable::today())
                    ->disabledDates(fn (): array => collect(range(0, 120))->map(fn (int $i) => CarbonImmutable::today()->addDays($i))->filter->isWeekend()->map->toDateString()->values()->all())
                    ->required()->live()
                    ->hint(fn (?string $state): ?string => $this->noticeHint($state))
                    ->hintIcon(fn (?string $state): ?string => $state ? (SmartWorking::isShortNotice(CarbonImmutable::parse($state)) ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle') : null)
                    ->hintColor(fn (?string $state): string => $state && SmartWorking::isShortNotice(CarbonImmutable::parse($state)) ? 'danger' : 'success'),
                Textarea::make('note')->label($isIt ? 'Nota per il referente (facoltativa)' : 'Note for the referent (optional)')->rows(2)->maxLength(500),
            ])
            ->action(function (array $data) use ($isIt): void {
                $this->attempt(fn () => SmartWorking::request($this->user(), CarbonImmutable::parse($data['day']), $data['note'] ?? null),
                    $isIt ? 'Richiesta inviata: riceverai una mail da Milo con la risposta' : 'Request sent: Milo will e-mail you the answer');
            });
    }

    /** Admins choose who approves smart working days. */
    public function smartWorkingReferentAction(): Action
    {
        $isIt = app()->getLocale() === 'it';

        return Action::make('smartWorkingReferent')
            ->label($isIt ? 'Referente smart working' : 'Smart working referent')
            ->icon('heroicon-m-user-circle')
            ->color('gray')
            ->visible(fn (): bool => Calendar::isAdmin($this->user()))
            ->modalHeading($isIt ? 'Referente smart working' : 'Smart working referent')
            ->modalDescription($isIt ? 'Riceve da Milo le richieste di smart working e le approva dalla mail.' : 'Gets the smart working requests from Milo and approves them from the e-mail.')
            ->fillForm(fn (): array => ['referent_id' => SmartWorking::referent()?->getKey()])
            ->schema([
                Select::make('referent_id')->label($isIt ? 'Referente' : 'Referent')
                    ->options(fn () => Calendar::people()->mapWithKeys(fn (User $u) => [$u->id => "{$u->name} · {$u->email}"])->all())
                    ->searchable()->required(),
            ])
            ->action(function (array $data) use ($isIt): void {
                abort_unless(Calendar::isAdmin($this->user()), 403);
                SmartWorking::setReferent((int) $data['referent_id']);
                Notification::make()->success()->title($isIt ? 'Referente salvato' : 'Referent saved')->send();
            });
    }

    private function noticeHint(?string $state): ?string
    {
        if (! $state) {
            return null;
        }
        $days = SmartWorking::noticeDays(CarbonImmutable::parse($state));
        $isIt = app()->getLocale() === 'it';
        if ($days < SmartWorking::NOTICE_DAYS) {
            return $isIt ? "Preavviso di {$days} ".($days === 1 ? 'giorno' : 'giorni').': meno di una settimana' : "{$days} days' notice: less than a week";
        }

        return $isIt ? "Preavviso di {$days} giorni" : "{$days} days' notice";
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
        $weekDays = $this->weekend ? 7 : 5;
        $days = $weekDays * self::WEEKS;
        // Not called "day": that would collide with the public $day URL property Livewire shares with the view.
        $selectedDay = $this->selectedDay();
        $teamPeople = Calendar::people($this->team);
        $event = $this->eventId ? Event::query()->with(['attendees.user:id,name', 'organizer:id,name', 'project:id,name'])->find($this->eventId) : null;

        return [
            'monday'    => $monday,
            'days'      => $days,
            'weekDays'  => $weekDays,
            'today'     => CarbonImmutable::today(),
            'selectedDay' => $selectedDay,
            'teams'     => Team::query()->orderBy('name')->get(['id', 'name']),
            'people'    => Calendar::people(),
            'board'     => $this->tab === 'presence' ? $this->weeks($monday, $weekDays, fn (CarbonImmutable $from) => Calendar::board($teamPeople, $from, $weekDays)) : null,
            'agenda'    => $this->tab === 'calendar' ? $this->weeks($monday, $weekDays, fn (CarbonImmutable $from) => Calendar::agenda($user, $from, $this->scope, $weekDays)) : null,
            'dayBoard'  => $this->tab === 'day' ? Calendar::dayTimeline($user, $teamPeople, $selectedDay) : null,
            'pending'   => Calendar::pendingFor($user),
            'event'     => $event,
            'eventView' => $event ? Calendar::view($event, $user) : null,
            'canEdit'   => $event ? Calendar::canEdit($user, $event) : false,
        ];
    }

    /**
     * self::WEEKS one-week boards/agendas side by side, so each week keeps its
     * own Monday and correctly skips its own weekend instead of spilling into the next.
     */
    private function weeks(CarbonImmutable $monday, int $weekDays, callable $oneWeek): array
    {
        $weeks = collect(range(0, self::WEEKS - 1))->map(fn (int $w) => $oneWeek($monday->addWeeks($w)));
        $first = $weeks->first();

        if (array_key_exists('rows', $first)) { // board: concat each person's cells across weeks
            $rows = collect($first['rows'])->map(fn ($row, $i) => [
                'user'  => $row['user'],
                'cells' => $weeks->flatMap(fn ($week) => $week['rows'][$i]['cells'])->all(),
                'now'   => $row['now'],
            ])->all();

            return ['rows' => $rows, 'from' => $monday, 'days' => $weekDays * self::WEEKS];
        }

        return [ // agenda: concat the day columns across weeks
            'days' => $weeks->flatMap(fn ($week) => $week['days'])->all(),
            'from' => $weeks->min('from'),
            'to'   => $weeks->max('to'),
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

    private function selectedDay(): CarbonImmutable
    {
        try {
            $day = $this->day !== '' ? CarbonImmutable::parse($this->day) : CarbonImmutable::today();
        } catch (\Throwable) {
            $day = CarbonImmutable::today();
        }

        return $day;
    }

    private function user(): User
    {
        return auth()->user();
    }
}
