<?php

namespace Huvant\Calendar\Support;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Huvant\Calendar\Filament\Pages\CalendarPage;
use Huvant\Calendar\Models\Attendee;
use Huvant\Calendar\Models\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * The company calendar: where everyone is on a day (office, smart working, trip,
 * away, leave), whether they can be reached right now, and meetings with invitees.
 */
class Calendar
{
    /** kind => [label, icon, colour, presence (says where one is), busy (occupies time)] */
    public const KINDS = [
        'meeting' => ['Meeting', 'heroicon-o-user-group', '#0075de', false, true],
        'remote'  => ['Remote', 'heroicon-o-home', '#19b2b2', true, false],
        'travel'  => ['Travel', 'heroicon-o-paper-airplane', '#884393', true, false],
        'away'    => ['Out of office', 'heroicon-o-no-symbol', '#d8141d', true, false],
        'focus'   => ['Focus time', 'heroicon-o-moon', '#ee7113', false, true],
        'other'   => ['Busy', 'heroicon-o-calendar', '#5b6b7f', false, true],
    ];

    public const RESPONSES = ['pending' => 'Awaiting reply', 'accepted' => 'Going', 'tentative' => 'Maybe', 'declined' => 'Not going'];

    /** Day presence => [label, tone] ; tone drives the colour. */
    public const PRESENCE = [
        'office'  => ['In the office', 'ok'],
        'remote'  => ['Remote', 'remote'],
        'travel'  => ['Travelling', 'limited'],
        'away'    => ['Out of office', 'off'],
        'leave'   => ['On leave', 'off'],
        'weekend' => ['Weekend', 'idle'],
    ];

    public static function kind(string $kind): array
    {
        return self::KINDS[$kind] ?? self::KINDS['other'];
    }

    public static function isAdmin(User $user): bool
    {
        return $user->roles()->get()->contains(fn (Role $role): bool => $role->isSystemRole());
    }

    /** The people of the company: active users with an employee record (everyone active otherwise). */
    public static function people(?int $teamId = null): Collection
    {
        return User::query()->where('is_active', true)
            ->when(Schema::hasTable('employees_employees'), fn ($q) => $q->whereIn('id', DB::table('employees_employees')->whereNull('deleted_at')->whereNotNull('user_id')->pluck('user_id')))
            ->when($teamId, fn ($q) => $q->whereIn('id', DB::table('user_team')->where('team_id', $teamId)->pluck('user_id')))
            ->orderBy('name')->get(['id', 'name', 'email']);
    }

    /**
     * Create or change an event. The organizer always takes part; invitees get a
     * notification, and a new time asks everyone to answer again.
     */
    public static function save(User $actor, array $data, ?Event $event = null): Event
    {
        if ($event && ! static::canEdit($actor, $event)) {
            throw new RuntimeException('Only the organizer can change this event.');
        }
        $kind = array_key_exists($data['kind'] ?? '', self::KINDS) ? $data['kind'] : 'meeting';
        [$start, $end] = static::range($data);
        $title = trim((string) ($data['title'] ?? '')) ?: static::kind($kind)[0];
        $people = collect($data['attendees'] ?? [])->map(fn ($id) => (int) $id)->filter()->unique();
        if (static::kind($kind)[3] && $people->isEmpty()) {
            $people = collect([(int) $actor->getKey()]); // "I'm in smart working" is about oneself by default
        }
        $organizerId = $event?->organizer_id ?? $actor->getKey();

        return DB::transaction(function () use ($event, $kind, $title, $start, $end, $data, $people, $organizerId, $actor): Event {
            $moved = $event && (! $event->starts_at->equalTo($start) || ! $event->ends_at->equalTo($end));
            $event ??= new Event;
            $event->fill([
                'kind'         => $kind,
                'title'        => mb_substr($title, 0, 200),
                'description'  => trim((string) ($data['description'] ?? '')) ?: null,
                'location'     => trim((string) ($data['location'] ?? '')) ?: null,
                'starts_at'    => $start,
                'ends_at'      => $end,
                'all_day'      => (bool) ($data['all_day'] ?? false),
                'private'      => (bool) ($data['private'] ?? false),
                'organizer_id' => $organizerId,
                'project_id'   => ($data['project_id'] ?? null) ?: null,
            ])->save();

            $wanted = $people->push((int) $organizerId)->unique();
            $existing = $event->attendees()->pluck('response', 'user_id');
            $event->attendees()->whereNotIn('user_id', $wanted)->delete();
            $invited = [];
            foreach ($wanted as $userId) {
                $isOrganizer = $userId === (int) $organizerId;
                $response = $isOrganizer || static::kind($kind)[3] ? 'accepted' : ($moved ? 'pending' : ($existing[$userId] ?? 'pending'));
                Attendee::query()->updateOrCreate(['event_id' => $event->getKey(), 'user_id' => $userId], ['response' => $response]);
                if (! $isOrganizer && (! $existing->has($userId) || $moved) && $userId !== (int) $actor->getKey()) {
                    $invited[] = $userId;
                }
            }
            if ($invited) {
                static::notify($invited, $event, $moved ? 'Moved: '.$event->title : ($kind === 'meeting' ? 'Invitation: ' : '').$event->title, $actor);
            }

            return $event->refresh();
        });
    }

    /**
     * Set where one is on a day ("office" clears it). Presence events that span
     * more days are split around it, so the other days stay as they were.
     */
    public static function setPresence(User $user, CarbonImmutable $day, string $kind): void
    {
        if ($kind !== 'office' && ! (self::KINDS[$kind][3] ?? false)) {
            throw new RuntimeException('Unknown presence.');
        }
        $day = $day->startOfDay();
        $next = $day->addDay();

        DB::transaction(function () use ($user, $day, $next, $kind): void {
            $events = Event::query()->with('attendees')
                ->whereIn('kind', ['remote', 'travel', 'away'])
                ->where('starts_at', '<', $next)->where('ends_at', '>', $day)
                ->whereHas('attendees', fn ($q) => $q->where('user_id', $user->getKey()))
                ->get();
            foreach ($events as $event) {
                $parts = [];
                if ($event->starts_at->lt($day)) {
                    $parts[] = [CarbonImmutable::parse($event->starts_at), $day];
                }
                if ($event->ends_at->gt($next)) {
                    $parts[] = [$next, CarbonImmutable::parse($event->ends_at)];
                }
                $onlyMine = $event->attendees->count() === 1 && (int) $event->organizer_id === (int) $user->getKey();
                if ($onlyMine) {
                    $event->delete();
                } else {
                    $event->attendees()->where('user_id', $user->getKey())->delete();
                }
                foreach ($parts as [$from, $to]) {
                    static::personal($user, $event->kind, $event->title, $from, $to, (bool) $event->all_day);
                }
            }
            if ($kind !== 'office') {
                static::personal($user, $kind, static::kind($kind)[0], $day, $next, true);
            }
        });
    }

    private static function personal(User $user, string $kind, string $title, CarbonImmutable $from, CarbonImmutable $to, bool $allDay): void
    {
        $event = Event::query()->create([
            'kind' => $kind, 'title' => $title, 'starts_at' => $from, 'ends_at' => $to, 'all_day' => $allDay, 'organizer_id' => $user->getKey(),
        ]);
        Attendee::query()->create(['event_id' => $event->getKey(), 'user_id' => $user->getKey(), 'response' => 'accepted']);
    }

    public static function delete(User $actor, Event $event): void
    {
        if (! static::canEdit($actor, $event)) {
            throw new RuntimeException('Only the organizer can delete this event.');
        }
        $others = $event->attendees()->where('user_id', '!=', $actor->getKey())->pluck('user_id')->all();
        if ($others && $event->ends_at->isFuture()) {
            static::notify($others, $event, 'Cancelled: '.$event->title, $actor, link: false);
        }
        $event->delete();
    }

    public static function respond(User $user, Event $event, string $response): void
    {
        if (! array_key_exists($response, self::RESPONSES) || $response === 'pending') {
            throw new RuntimeException('Invalid reply.');
        }
        $attendee = $event->attendees()->where('user_id', $user->getKey())->first();
        if (! $attendee) {
            throw new RuntimeException('You are not invited to this event.');
        }
        $attendee->update(['response' => $response]);
        if ($event->organizer_id && (int) $event->organizer_id !== (int) $user->getKey()) {
            static::notify([$event->organizer_id], $event, $user->name.' replied “'.self::RESPONSES[$response].'” to “'.$event->title.'”', $user);
        }
    }

    public static function canEdit(User $user, Event $event): bool
    {
        return (int) $event->organizer_id === (int) $user->getKey() || static::isAdmin($user);
    }

    /** Invitees already busy (other events, time off) during the slot. */
    public static function conflicts(array $userIds, CarbonImmutable $start, CarbonImmutable $end, ?int $ignoreEventId = null): array
    {
        $userIds = array_values(array_filter(array_map('intval', $userIds)));
        if (! $userIds || $end->lte($start)) {
            return [];
        }
        $names = User::query()->whereIn('id', $userIds)->pluck('name', 'id');
        $out = [];
        Event::query()->with('attendees')
            ->where('starts_at', '<', $end)->where('ends_at', '>', $start)
            ->when($ignoreEventId, fn ($q) => $q->whereKeyNot($ignoreEventId))
            ->whereHas('attendees', fn ($q) => $q->whereIn('user_id', $userIds)->where('response', '!=', 'declined'))
            ->get()
            ->each(function (Event $e) use (&$out, $userIds, $names): void {
                foreach ($e->attendees->whereIn('user_id', $userIds)->where('response', '!=', 'declined') as $a) {
                    $out[] = $names[$a->user_id].': '.static::kind($e->kind)[0].($e->private ? '' : ' “'.$e->title.'”')
                        .($e->all_day ? ' (all day)' : ' '.$e->starts_at->format('H:i').'–'.$e->ends_at->format('H:i'));
                }
            });
        foreach (static::leaves($userIds, $start, $end) as $leave) {
            $out[] = $names[$leave->user_id].': on leave';
        }

        return array_values(array_unique($out));
    }

    /**
     * Presence board: for each person and day where they are and how many
     * meetings they have; for today, whether they can be reached now.
     */
    public static function board(Collection $people, CarbonImmutable $from, int $days, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $to = $from->addDays($days);
        $ids = $people->pluck('id')->all();
        $events = Event::query()->with('attendees')
            ->where('starts_at', '<', $to)->where('ends_at', '>', $from)
            ->whereHas('attendees', fn ($q) => $q->whereIn('user_id', $ids))
            ->orderBy('starts_at')->get();
        $leaves = static::leaves($ids, $from, $to);

        $rows = $people->map(function (User $person) use ($events, $leaves, $from, $days, $now) {
            $mine = $events->filter(fn (Event $e) => $e->attendees->contains(fn ($a) => (int) $a->user_id === (int) $person->id && $a->response !== 'declined'));
            $myLeaves = $leaves->where('user_id', $person->id);
            $cells = [];
            for ($i = 0; $i < $days; $i++) {
                $day = $from->addDays($i);
                $cells[] = static::dayCell($person, $day, $mine, $myLeaves);
            }

            return ['user' => $person, 'cells' => $cells, 'now' => static::nowStatus($person, $now, $mine, $myLeaves)];
        });

        return ['rows' => $rows->all(), 'from' => $from, 'days' => $days];
    }

    /** A week of events for the agenda: all-day ones apart, timed ones laid out in lanes. */
    public static function agenda(User $viewer, CarbonImmutable $monday, string $scope, int $days = 7): array
    {
        $end = $monday->addDays($days);
        $query = Event::query()->with(['attendees.user:id,name', 'organizer:id,name', 'project:id,name'])
            ->where('starts_at', '<', $end)->where('ends_at', '>', $monday)->orderBy('starts_at');
        if ($scope === 'me') {
            $query->whereHas('attendees', fn ($q) => $q->where('user_id', $viewer->getKey())->where('response', '!=', 'declined'));
        } elseif (ctype_digit($scope)) {
            $query->whereHas('attendees', fn ($q) => $q->where('user_id', (int) $scope)->where('response', '!=', 'declined'));
        }
        $events = $query->get();

        $first = 8 * 60;
        $last = 19 * 60;
        foreach ($events->where('all_day', false) as $e) {
            $first = min($first, (int) $e->starts_at->format('G') * 60);
            $last = max($last, $e->ends_at->isSameDay($e->starts_at) ? (int) ceil(((int) $e->ends_at->format('G') * 60 + (int) $e->ends_at->format('i')) / 60) * 60 : 24 * 60);
        }

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $monday->addDays($i);
            $today = $events->filter(fn (Event $e) => $e->starts_at->lt($day->addDay()) && $e->ends_at->gt($day));
            $timed = $today->filter(fn (Event $e) => ! $e->all_day && $e->starts_at->isSameDay($day) && $e->ends_at->isSameDay($day))->values();
            $allDay = $today->reject(fn (Event $e) => $timed->contains($e))->values();
            $out[] = [
                'date'   => $day,
                'allDay' => $allDay->map(fn (Event $e) => static::view($e, $viewer))->all(),
                'timed'  => static::lanes($timed->map(fn (Event $e) => static::view($e, $viewer) + [
                    'from' => (int) $e->starts_at->format('G') * 60 + (int) $e->starts_at->format('i'),
                    'to'   => (int) $e->ends_at->format('G') * 60 + (int) $e->ends_at->format('i'),
                ])->all()),
            ];
        }

        return ['days' => $out, 'from' => $first, 'to' => $last];
    }

    /** What the viewer may see of an event: someone else's private event is just "busy". */
    public static function view(Event $event, User $viewer): array
    {
        $involved = static::isAdmin($viewer) || $event->attendees->contains('user_id', $viewer->getKey()) || (int) $event->organizer_id === (int) $viewer->getKey();
        $masked = $event->private && ! $involved;
        [$label, $icon, $color] = static::kind($event->kind);
        $mine = $event->attendees->firstWhere('user_id', $viewer->getKey());

        return [
            'id'       => $event->id,
            'kind'     => $event->kind,
            'label'    => $label,
            'icon'     => $icon,
            'color'    => $color,
            'title'    => $masked ? 'Busy' : $event->title,
            'masked'   => $masked,
            'time'     => $event->all_day ? 'All day' : $event->starts_at->format('H:i').'–'.$event->ends_at->format('H:i'),
            'location' => $masked ? null : $event->location,
            'people'   => $masked ? [] : $event->attendees->map(fn ($a) => ['id' => $a->user_id, 'name' => $a->user?->name, 'response' => $a->response])->all(),
            'response' => $mine?->response,
        ];
    }

    public static function pendingFor(User $user): Collection
    {
        return Event::query()->with('organizer:id,name')
            ->where('ends_at', '>', now())
            ->whereHas('attendees', fn ($q) => $q->where('user_id', $user->getKey())->where('response', 'pending'))
            ->orderBy('starts_at')->get();
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    public static function range(array $data): array
    {
        $date = CarbonImmutable::parse($data['date'] ?? 'today')->startOfDay();
        $until = isset($data['date_to']) && $data['date_to'] ? CarbonImmutable::parse($data['date_to'])->startOfDay() : $date;
        if (! empty($data['all_day'])) {
            $start = $date;
            $end = $until->addDay();
        } else {
            $start = static::at($date, (string) ($data['from'] ?? '09:00'));
            $end = static::at($until, (string) ($data['to'] ?? '10:00'));
        }
        if ($end->lte($start)) {
            throw new RuntimeException('The end must come after the start.');
        }
        if ($start->diffInDays($end) > 62) {
            throw new RuntimeException('An event can last at most two months.');
        }

        return [$start, $end];
    }

    private static function at(CarbonImmutable $day, string $time): CarbonImmutable
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})/', trim($time), $m) || (int) $m[1] > 23 || (int) $m[2] > 59) {
            throw new RuntimeException('Invalid time: '.$time);
        }

        return $day->setTime((int) $m[1], (int) $m[2]);
    }

    private static function dayCell(User $person, CarbonImmutable $day, Collection $events, Collection $leaves): array
    {
        $dayEnd = $day->addDay();
        $today = $events->filter(fn (Event $e) => $e->starts_at->lt($dayEnd) && $e->ends_at->gt($day));
        $presence = $leaves->first(fn ($l) => CarbonImmutable::parse($l->request_date_from)->lte($day) && CarbonImmutable::parse($l->request_date_to)->gte($day)) ? 'leave' : null;
        foreach (['away', 'travel', 'remote'] as $kind) {
            $presence ??= $today->contains('kind', $kind) ? $kind : null;
        }
        $presence ??= $day->isWeekend() ? 'weekend' : 'office';

        return [
            'date'     => $day,
            'presence' => $presence,
            'meetings' => $today->filter(fn (Event $e) => static::kind($e->kind)[4])->count(),
            'events'   => $today->values(),
        ];
    }

    /** [label, tone, detail] for right now. */
    private static function nowStatus(User $person, CarbonImmutable $now, Collection $events, Collection $leaves): array
    {
        if ($leaves->first(fn ($l) => CarbonImmutable::parse($l->request_date_from)->startOfDay()->lte($now) && CarbonImmutable::parse($l->request_date_to)->endOfDay()->gte($now))) {
            return ['On leave', 'off', null];
        }
        $current = $events->filter(fn (Event $e) => $e->starts_at->lte($now) && $e->ends_at->gt($now));
        if ($away = $current->firstWhere('kind', 'away')) {
            return ['Out of office', 'off', $away->all_day ? null : 'until '.$away->ends_at->format('H:i')];
        }
        if ($busy = $current->first(fn (Event $e) => ! $e->all_day && in_array($e->kind, ['meeting', 'focus', 'other'], true))) {
            return [$busy->kind === 'focus' ? 'Focus time' : ($busy->kind === 'meeting' ? 'In a meeting' : 'Busy'), 'busy', 'until '.$busy->ends_at->format('H:i')];
        }
        if (! static::inWorkingHours($person, $now)) {
            return ['Off hours', 'idle', null];
        }
        if ($current->contains('kind', 'travel')) {
            return ['Travelling', 'limited', null];
        }
        $next = $events->filter(fn (Event $e) => ! $e->all_day && static::kind($e->kind)[4] && $e->starts_at->gt($now) && $e->starts_at->isSameDay($now))->sortBy('starts_at')->first();

        return [$current->contains('kind', 'remote') ? 'Available · remote' : 'Available', 'ok', $next ? 'meeting at '.$next->starts_at->format('H:i') : null];
    }

    private static function inWorkingHours(User $person, CarbonImmutable $now): bool
    {
        $hour = (float) $now->format('G') + (int) $now->format('i') / 60;
        $calendarId = Schema::hasTable('employees_employees')
            ? DB::table('employees_employees')->where('user_id', $person->getKey())->whereNull('deleted_at')->value('calendar_id')
            : null;
        if (! $calendarId) {
            return ! $now->isWeekend() && $hour >= 9 && $hour < 18;
        }

        return DB::table('calendar_attendances')->where('calendar_id', $calendarId)
            ->where('day_of_week', strtolower($now->englishDayOfWeek))->where('day_period', '!=', 'lunch')
            ->where('hour_from', '<=', $hour)->where('hour_to', '>', $hour)->exists();
    }

    /** Approved or requested time off overlapping the range. */
    private static function leaves(array $userIds, CarbonImmutable $from, CarbonImmutable $to): Collection
    {
        if (! Schema::hasTable('time_off_leaves')) {
            return collect();
        }

        return DB::table('time_off_leaves')->whereIn('user_id', $userIds)
            ->whereIn('state', ['validate_one', 'validate_two'])
            ->whereDate('request_date_from', '<', $to->toDateString())->whereDate('request_date_to', '>=', $from->toDateString())
            ->get(['user_id', 'request_date_from', 'request_date_to', 'state']);
    }

    /** Side by side when events overlap. */
    private static function lanes(array $items): array
    {
        usort($items, fn ($a, $b) => [$a['from'], $b['to']] <=> [$b['from'], $a['to']]);
        $out = [];
        $cluster = [];
        $clusterEnd = -1;
        $flush = function () use (&$cluster, &$out): void {
            $laneEnds = [];
            foreach ($cluster as &$item) {
                $lane = 0;
                while (isset($laneEnds[$lane]) && $laneEnds[$lane] > $item['from']) {
                    $lane++;
                }
                $laneEnds[$lane] = $item['to'];
                $item['lane'] = $lane;
            }
            unset($item);
            foreach ($cluster as $item) {
                $out[] = $item + ['lanes' => max(1, count($laneEnds))];
            }
            $cluster = [];
        };
        foreach ($items as $item) {
            if ($cluster && $item['from'] >= $clusterEnd) {
                $flush();
                $clusterEnd = -1;
            }
            $cluster[] = $item;
            $clusterEnd = max($clusterEnd, $item['to']);
        }
        if ($cluster) {
            $flush();
        }

        return $out;
    }

    private static function notify(array $userIds, Event $event, string $title, User $from, bool $link = true): void
    {
        $users = User::query()->whereIn('id', $userIds)->get();
        if ($users->isEmpty()) {
            return;
        }
        $when = $event->all_day
            ? $event->starts_at->format('d/m').($event->ends_at->subDay()->isSameDay($event->starts_at) ? '' : '–'.$event->ends_at->subDay()->format('d/m'))
            : $event->starts_at->format('d/m H:i').'–'.$event->ends_at->format('H:i');
        $notification = Notification::make()->title($title)->body($when.' · from '.$from->name)->icon(static::kind($event->kind)[1]);
        if ($link && class_exists(CalendarPage::class)) {
            $notification->actions([
                Action::make('open')->label('Open in calendar')->button()
                    ->url(rescue(fn () => CalendarPage::getUrl(['event' => $event->getKey(), 'view' => 'agenda', 'week' => $event->starts_at->startOfWeek()->toDateString()]), '#', false)),
            ]);
        }
        $notification->sendToDatabase($users);
    }
}
