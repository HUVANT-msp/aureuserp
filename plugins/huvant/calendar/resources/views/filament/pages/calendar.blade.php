@php
    use Huvant\Calendar\Support\Calendar;
    $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $initials = fn (?string $n) => collect(preg_split('/\s+/', trim((string) $n)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $personColor = fn (int $id) => class_exists(\Huvant\Tasks\Support\Palette::class) ? \Huvant\Tasks\Support\Palette::person($id) : '#8b8782';
    // Each week keeps its own Monday, so the last visible day is the final week's last weekday, not day $days-1 from $monday.
    $weekOf = fn (int $i) => $monday->addWeeks(intdiv($i, $weekDays))->addDays($i % $weekDays);
    $end = $weekOf($days - 1);
@endphp
<x-filament-panels::page>
    <div class="hv-cal">
        @if ($pending->isNotEmpty())
            <div class="hv-cal-pending">
                <x-filament::icon icon="heroicon-m-envelope" class="h-5 w-5" />
                <span><b>{{ $pending->count() }} {{ $pending->count() === 1 ? 'invitation' : 'invitations' }}</b> awaiting your reply:</span>
                @foreach ($pending->take(4) as $invite)
                    <button type="button" wire:click="openEvent({{ $invite->id }})">{{ $invite->title }} · {{ $invite->starts_at->format('D d/m H:i') }}</button>
                @endforeach
            </div>
        @endif

        <div class="hv-cal-toolbar">
            <div class="hv-cal-seg" role="tablist" aria-label="View">
                <button type="button" role="tab" aria-selected="{{ $tab === 'calendar' ? 'true' : 'false' }}" wire:click="setTab('calendar')">
                    <x-filament::icon icon="heroicon-m-calendar-days" class="h-4 w-4" />Calendar
                </button>
                <button type="button" role="tab" aria-selected="{{ $tab === 'day' ? 'true' : 'false' }}" wire:click="setTab('day')">
                    <x-filament::icon icon="heroicon-m-clock" class="h-4 w-4" />Day
                </button>
                <button type="button" role="tab" aria-selected="{{ $tab === 'presence' ? 'true' : 'false' }}" wire:click="setTab('presence')">
                    <x-filament::icon icon="heroicon-m-users" class="h-4 w-4" />Who's where
                </button>
            </div>
            @if ($tab === 'day')
                <div class="hv-cal-weeknav">
                    <x-filament::icon-button icon="heroicon-m-chevron-left" color="gray" wire:click="shiftDay(-1)" label="Previous day" />
                    <span>{{ $selectedDay->format('D j M Y') }}</span>
                    <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" wire:click="shiftDay(1)" label="Next day" />
                    @unless ($selectedDay->isSameDay($today))
                        <x-filament::button size="sm" color="gray" wire:click="todayDay">Today</x-filament::button>
                    @endunless
                </div>
            @else
                <div class="hv-cal-weeknav">
                    <x-filament::icon-button icon="heroicon-m-chevron-left" color="gray" wire:click="shiftWeek(-1)" label="Previous week" />
                    <span>{{ $monday->format('j M') }} – {{ $end->format('j M Y') }}</span>
                    <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" wire:click="shiftWeek(1)" label="Next week" />
                    @unless ($monday->isSameDay($today->startOfWeek()))
                        <x-filament::button size="sm" color="gray" wire:click="thisWeek">Today</x-filament::button>
                    @endunless
                </div>
            @endif
            <div class="hv-cal-filters">
                @if ($tab === 'presence' || $tab === 'day')
                    <select wire:model.live="team" aria-label="Team">
                        <option value="">Everyone</option>
                        @foreach ($teams as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
                    </select>
                @else
                    <select wire:model.live="scope" aria-label="Whose calendar">
                        <option value="me">My events</option>
                        <option value="all">Everyone</option>
                        <optgroup label="Person">
                            @foreach ($people as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
                        </optgroup>
                    </select>
                @endif
                @unless ($tab === 'day')
                    <label class="hv-cal-check"><input type="checkbox" wire:model.live="weekend" /> Weekend</label>
                @endunless
            </div>
        </div>

        {{-- Who's where --}}
        @if ($tab === 'presence')
            <div class="hv-cal-board" style="--days: {{ $days }}">
                <div class="hv-cal-row hv-cal-head">
                    <span class="hv-cal-person">Person</span>
                    <span class="hv-cal-now">Now</span>
                    @for ($i = 0; $i < $days; $i++)
                        @php $d = $weekOf($i); @endphp
                        <span @class(['hv-cal-day', 'is-today' => $d->isSameDay($today)])>{{ $dayNames[$i % 7] }} <b>{{ $d->day }}</b></span>
                    @endfor
                </div>
                @foreach ($board['rows'] as $row)
                    @php [$nowLabel, $nowTone, $nowDetail] = $row['now']; @endphp
                    <div class="hv-cal-row" wire:key="p-{{ $row['user']->id }}">
                        <button type="button" class="hv-cal-person" wire:click="showPerson({{ $row['user']->id }})" title="Open {{ $row['user']->name }}'s week">
                            <span class="hv-cal-avatar" style="--ac: {{ $personColor($row['user']->id) }}">{{ $initials($row['user']->name) }}</span>
                            {{ $row['user']->name }}
                        </button>
                        <span class="hv-cal-now">
                            @if ($monday->lte($today) && $end->gte($today))
                                <span class="hv-cal-status tone-{{ $nowTone }}"><i></i>{{ $nowLabel }}</span>
                                @if ($nowDetail)<small>{{ $nowDetail }}</small>@endif
                            @else
                                <span class="hv-cal-muted">—</span>
                            @endif
                        </span>
                        @php $isMe = (int) $row['user']->id === (int) auth()->id(); @endphp
                        @foreach ($row['cells'] as $cell)
                            @php [$label, $tone] = Calendar::PRESENCE[$cell['presence']]; @endphp
                            @if ($isMe && $cell['presence'] !== 'leave')
                                <span class="hv-cal-mine" x-data="{ open: false }" x-on:keydown.escape="open = false" x-on:click.outside="open = false">
                                    <button type="button" @class(['hv-cal-cell', 'is-editable', 'tone-'.$tone, 'is-today' => $cell['date']->isSameDay($today)])
                                            x-on:click="open = ! open" :aria-expanded="open" aria-haspopup="menu"
                                            aria-label="Where are you on {{ $cell['date']->format('l j F') }}? Now: {{ $label }}">
                                        <span class="hv-cal-presence">{{ $cell['presence'] === 'office' ? 'Office' : $label }}</span>
                                        @if ($cell['meetings'])<span class="hv-cal-meetings">{{ $cell['meetings'] }} {{ $cell['meetings'] === 1 ? 'meeting' : 'meetings' }}</span>@endif
                                        <x-filament::icon icon="heroicon-m-pencil" class="hv-cal-edit h-3 w-3" />
                                    </button>
                                    <span class="hv-cal-menu" role="menu" x-show="open" x-cloak x-transition.opacity>
                                        @foreach (['office' => ['Office', 'heroicon-m-building-office'], 'travel' => ['Travel', 'heroicon-m-paper-airplane'], 'away' => ['Out of office', 'heroicon-m-no-symbol']] as $kind => [$kindLabel, $kindIcon])
                                            <button type="button" role="menuitemradio" aria-checked="{{ $cell['presence'] === $kind ? 'true' : 'false' }}"
                                                    wire:click="setMyDay('{{ $cell['date']->toDateString() }}', '{{ $kind }}')" x-on:click="open = false">
                                                <x-filament::icon :icon="$kindIcon" class="h-4 w-4" />{{ $kindLabel }}
                                            </button>
                                        @endforeach
                                        {{-- Smart working only with the referent's approval. --}}
                                        <button type="button" role="menuitem"
                                                wire:click="mountAction('smartWorkingRequest', { day: '{{ $cell['date']->toDateString() }}' })" x-on:click="open = false">
                                            <x-filament::icon icon="heroicon-m-home" class="h-4 w-4" />{{ app()->getLocale() === 'it' ? 'Chiedi smart working' : 'Request smart working' }}
                                        </button>
                                    </span>
                                </span>
                                @continue
                            @endif
                            <span @class(['hv-cal-cell', 'tone-'.$tone, 'is-today' => $cell['date']->isSameDay($today)])
                                  title="{{ $label }}@foreach ($cell['events'] as $e)&#10;{{ $e->all_day ? 'All day' : $e->starts_at->format('H:i') }} {{ $e->private && ! $e->attendees->contains('user_id', auth()->id()) ? 'Busy' : $e->title }}@endforeach">
                                <span class="hv-cal-presence">{{ $cell['presence'] === 'office' ? 'Office' : $label }}</span>
                                @if ($cell['meetings'])<span class="hv-cal-meetings">{{ $cell['meetings'] }} {{ $cell['meetings'] === 1 ? 'meeting' : 'meetings' }}</span>@endif
                            </span>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <ul class="hv-cal-legend">
                @foreach (['ok' => 'In the office', 'remote' => 'Remote', 'limited' => 'Travelling', 'off' => 'Out of office / on leave'] as $tone => $label)
                    <li><span class="hv-cal-swatch tone-{{ $tone }}"></span>{{ $label }}</li>
                @endforeach
                <li class="hv-cal-muted">Click a person to see their week; click a day on your own row to change where you are.</li>
            </ul>
        @endif

        {{-- Calendar (two-week agenda) --}}
        @if ($tab === 'calendar')
            @php
                $span = max(60, $agenda['to'] - $agenda['from']);
                $pct = fn (int $m) => round(($m - $agenda['from']) / $span * 100, 3);
            @endphp
            <div class="hv-cal-agenda" style="--days: {{ $days }}">
                <div class="hv-cal-agrow hv-cal-aghead">
                    <span></span>
                    @foreach ($agenda['days'] as $i => $d)
                        <span @class(['hv-cal-agday', 'is-today' => $d['date']->isSameDay($today)])>{{ $dayNames[$i % 7] }} <b>{{ $d['date']->day }}</b></span>
                    @endforeach
                </div>
                <div class="hv-cal-agrow hv-cal-allday">
                    <span class="hv-cal-hourlabel">All day</span>
                    @foreach ($agenda['days'] as $d)
                        <div>
                            @foreach ($d['allDay'] as $e)
                                <button type="button" class="hv-cal-chip" style="--ec: {{ $e['color'] }}" wire:click="openEvent({{ $e['id'] }})">
                                    <x-filament::icon :icon="$e['icon']" class="h-3.5 w-3.5" />
                                    <span>{{ $e['title'] }}@if ($scope === 'all' && count($e['people']) === 1) · {{ $e['people'][0]['name'] }}@endif</span>
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div class="hv-cal-agrow hv-cal-grid">
                    <div class="hv-cal-hours">
                        @for ($m = $agenda['from']; $m < $agenda['to']; $m += 60)
                            <span style="top: {{ $pct($m) }}%">{{ intdiv($m, 60) }}:00</span>
                        @endfor
                    </div>
                    @foreach ($agenda['days'] as $d)
                        <div @class(['hv-cal-col', 'is-today' => $d['date']->isSameDay($today)])>
                            @for ($m = $agenda['from']; $m < $agenda['to']; $m += 60)
                                <span class="hv-cal-line" style="top: {{ $pct($m) }}%"></span>
                            @endfor
                            @if ($d['date']->isSameDay($today))
                                @php $nowM = (int) now()->format('G') * 60 + (int) now()->format('i'); @endphp
                                @if ($nowM >= $agenda['from'] && $nowM <= $agenda['to'])<span class="hv-cal-nowline" style="top: {{ $pct($nowM) }}%"></span>@endif
                            @endif
                            @foreach ($d['timed'] as $e)
                                <button type="button" @class(['hv-cal-event', 'is-pending' => $e['response'] === 'pending', 'is-masked' => $e['masked']])
                                        style="--ec: {{ $e['color'] }}; top: {{ $pct($e['from']) }}%; height: {{ max(2.5, $pct($e['to']) - $pct($e['from'])) }}%; left: calc({{ $e['lane'] }} / {{ $e['lanes'] }} * 100%); width: calc(100% / {{ $e['lanes'] }} - 3px)"
                                        wire:click="openEvent({{ $e['id'] }})" title="{{ $e['time'] }} {{ $e['title'] }}">
                                    <b>{{ $e['title'] }}</b>
                                    <small>{{ $e['time'] }}@if ($e['location']) · {{ $e['location'] }}@endif</small>
                                </button>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Day: who's where, with a timeline of today's events --}}
        @if ($tab === 'day')
            @php
                $dbSpan = max(60, $dayBoard['to'] - $dayBoard['from']);
                $dpct = fn (int $m) => round((max($dayBoard['from'], min($dayBoard['to'], $m)) - $dayBoard['from']) / $dbSpan * 100, 3);
                $nowM = (int) now()->format('G') * 60 + (int) now()->format('i');
                $isToday = $selectedDay->isSameDay($today);
            @endphp
            <div class="hv-cal-dayboard">
                <div class="hv-cal-dayrow hv-cal-dayhead">
                    <span class="hv-cal-person">Person</span>
                    <div class="hv-cal-daytimeline">
                        @for ($m = $dayBoard['from']; $m <= $dayBoard['to']; $m += 60)
                            <span class="hv-cal-dayhour" style="left: {{ $dpct($m) }}%">{{ intdiv($m, 60) }}:00</span>
                        @endfor
                        @if ($isToday && $nowM >= $dayBoard['from'] && $nowM <= $dayBoard['to'])
                            <span class="hv-cal-daynowline" style="left: {{ $dpct($nowM) }}%"></span>
                        @endif
                    </div>
                </div>
                @foreach ($dayBoard['rows'] as $row)
                    <div class="hv-cal-dayrow" wire:key="dd-{{ $row['user']->id }}">
                        <button type="button" class="hv-cal-person" wire:click="showPerson({{ $row['user']->id }})" title="Open {{ $row['user']->name }}'s calendar">
                            <span class="hv-cal-avatar" style="--ac: {{ $personColor($row['user']->id) }}">{{ $initials($row['user']->name) }}</span>
                            {{ $row['user']->name }}
                        </button>
                        <div @class(['hv-cal-daytimeline', 'tone-'.Calendar::PRESENCE[$row['presence']][1]])>
                            @for ($m = $dayBoard['from']; $m < $dayBoard['to']; $m += 60)
                                <span class="hv-cal-dayline" style="left: {{ $dpct($m) }}%"></span>
                            @endfor
                            @if ($isToday && $nowM >= $dayBoard['from'] && $nowM <= $dayBoard['to'])
                                <span class="hv-cal-daynowline" style="left: {{ $dpct($nowM) }}%"></span>
                            @endif
                            @foreach ($row['segments'] as $seg)
                                <button type="button" class="hv-cal-dayevent" style="--ec: {{ $seg['color'] }}; left: {{ $dpct($seg['from']) }}%; width: {{ max(1.5, $dpct($seg['to']) - $dpct($seg['from'])) }}%"
                                        wire:click="openEvent({{ $seg['id'] }})" title="{{ $seg['label'] }}">
                                    <span>{{ $seg['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <ul class="hv-cal-legend">
                @foreach (['remote' => 'Remote', 'limited' => 'Travelling', 'off' => 'Out of office / on leave'] as $tone => $label)
                    <li><span class="hv-cal-swatch tone-{{ $tone }}"></span>{{ $label }}</li>
                @endforeach
                <li class="hv-cal-muted">Blocks show today's meetings and busy time between {{ intdiv($dayBoard['from'], 60) }}:00 and {{ intdiv($dayBoard['to'], 60) }}:00, colour-coded by type; click one to see it, click a person to open their calendar.</li>
            </ul>
        @endif
    </div>

    {{-- Event details --}}
    <x-filament::modal id="hv-cal-event" width="lg" x-on:modal-closed.window="if ($event.detail.id === 'hv-cal-event') $wire.closeEvent()">
        @if ($eventView)
            <x-slot name="heading">
                <span class="hv-cal-modal-head" style="--ec: {{ $eventView['color'] }}">
                    <x-filament::icon :icon="$eventView['icon']" class="h-5 w-5" />{{ $eventView['title'] }}
                </span>
            </x-slot>
            <x-slot name="description">
                {{ $event->starts_at->format('l j F Y') }} · {{ $eventView['time'] }}
            </x-slot>
            <div class="hv-cal-detail">
                <dl>
                    <dt>Type</dt><dd>{{ $eventView['label'] }}@if ($event->private) · private @endif</dd>
                    @if ($event->organizer)<dt>Organizer</dt><dd>{{ $event->organizer->name }}</dd>@endif
                    @if ($eventView['location'])
                        <dt>Where</dt>
                        <dd>@if (str_starts_with($eventView['location'], 'http'))<a href="{{ $eventView['location'] }}" target="_blank" rel="noreferrer">{{ $eventView['location'] }}</a>@else{{ $eventView['location'] }}@endif</dd>
                    @endif
                    @if (! $eventView['masked'] && $event->project)<dt>Project</dt><dd>{{ $event->project->name }}</dd>@endif
                </dl>
                @if (! $eventView['masked'] && $event->description)
                    <p class="hv-cal-notes">{{ $event->description }}</p>
                @endif
                @if ($eventView['people'])
                    <ul class="hv-cal-people">
                        @foreach ($eventView['people'] as $p)
                            <li>
                                <span class="hv-cal-avatar" style="--ac: {{ $personColor($p['id']) }}">{{ $initials($p['name']) }}</span>
                                <span class="hv-cal-grow">{{ $p['name'] }}</span>
                                <span class="hv-cal-reply reply-{{ $p['response'] }}">{{ Calendar::RESPONSES[$p['response']] ?? $p['response'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if ($eventView['response'] && (int) $event->organizer_id !== (int) auth()->id() && ! Calendar::kind($event->kind)[3])
                    <div class="hv-cal-respond">
                        <span>Are you going?</span>
                        @foreach (['accepted' => 'Yes', 'tentative' => 'Maybe', 'declined' => 'No'] as $value => $label)
                            <x-filament::button size="sm" :color="$eventView['response'] === $value ? 'primary' : 'gray'" wire:click="respond('{{ $value }}')">{{ $label }}</x-filament::button>
                        @endforeach
                    </div>
                @endif
            </div>
            @if ($canEdit)
                <x-slot name="footerActions">
                    {{ $this->editEventAction }}
                    <x-filament::button color="danger" size="sm" wire:click="deleteEvent" wire:confirm="Delete this event? Invitees will be notified.">Delete</x-filament::button>
                </x-slot>
            @endif
        @endif
    </x-filament::modal>
</x-filament-panels::page>
