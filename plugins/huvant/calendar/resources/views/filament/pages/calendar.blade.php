@php
    use Huvant\Calendar\Support\Calendar;
    $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $initials = fn (?string $n) => collect(preg_split('/\s+/', trim((string) $n)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $personColor = fn (int $id) => class_exists(\Huvant\Tasks\Support\Palette::class) ? \Huvant\Tasks\Support\Palette::person($id) : '#8b8782';
    $end = $monday->addDays($days - 1);
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
                <button type="button" role="tab" aria-selected="{{ $tab === 'presence' ? 'true' : 'false' }}" wire:click="setTab('presence')">
                    <x-filament::icon icon="heroicon-m-users" class="h-4 w-4" />Who's where
                </button>
                <button type="button" role="tab" aria-selected="{{ $tab === 'agenda' ? 'true' : 'false' }}" wire:click="setTab('agenda')">
                    <x-filament::icon icon="heroicon-m-calendar-days" class="h-4 w-4" />Week
                </button>
            </div>
            <div class="hv-cal-weeknav">
                <x-filament::icon-button icon="heroicon-m-chevron-left" color="gray" wire:click="shiftWeek(-1)" label="Previous week" />
                <span>{{ $monday->format('j M') }} – {{ $end->format('j M Y') }}</span>
                <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" wire:click="shiftWeek(1)" label="Next week" />
                @unless ($monday->isSameDay($today->startOfWeek()))
                    <x-filament::button size="sm" color="gray" wire:click="thisWeek">Today</x-filament::button>
                @endunless
            </div>
            <div class="hv-cal-filters">
                @if ($tab === 'presence')
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
                <label class="hv-cal-check"><input type="checkbox" wire:model.live="weekend" /> Weekend</label>
            </div>
        </div>

        {{-- Who's where --}}
        @if ($tab === 'presence')
            <div class="hv-cal-board" style="--days: {{ $days }}">
                <div class="hv-cal-row hv-cal-head">
                    <span class="hv-cal-person">Person</span>
                    <span class="hv-cal-now">Now</span>
                    @for ($i = 0; $i < $days; $i++)
                        @php $d = $monday->addDays($i); @endphp
                        <span @class(['hv-cal-day', 'is-today' => $d->isSameDay($today)])>{{ $dayNames[$i] }} <b>{{ $d->day }}</b></span>
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
                        @foreach ($row['cells'] as $cell)
                            @php [$label, $tone] = Calendar::PRESENCE[$cell['presence']]; @endphp
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
                <li class="hv-cal-muted">Click a person to see their week.</li>
            </ul>
        @endif

        {{-- Week agenda --}}
        @if ($tab === 'agenda')
            @php
                $span = max(60, $agenda['to'] - $agenda['from']);
                $pct = fn (int $m) => round(($m - $agenda['from']) / $span * 100, 3);
            @endphp
            <div class="hv-cal-agenda" style="--days: {{ $days }}">
                <div class="hv-cal-agrow hv-cal-aghead">
                    <span></span>
                    @foreach ($agenda['days'] as $i => $day)
                        <span @class(['hv-cal-agday', 'is-today' => $day['date']->isSameDay($today)])>{{ $dayNames[$i] }} <b>{{ $day['date']->day }}</b></span>
                    @endforeach
                </div>
                <div class="hv-cal-agrow hv-cal-allday">
                    <span class="hv-cal-hourlabel">All day</span>
                    @foreach ($agenda['days'] as $day)
                        <div>
                            @foreach ($day['allDay'] as $e)
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
                    @foreach ($agenda['days'] as $day)
                        <div @class(['hv-cal-col', 'is-today' => $day['date']->isSameDay($today)])>
                            @for ($m = $agenda['from']; $m < $agenda['to']; $m += 60)
                                <span class="hv-cal-line" style="top: {{ $pct($m) }}%"></span>
                            @endfor
                            @if ($day['date']->isSameDay($today))
                                @php $nowM = (int) now()->format('G') * 60 + (int) now()->format('i'); @endphp
                                @if ($nowM >= $agenda['from'] && $nowM <= $agenda['to'])<span class="hv-cal-nowline" style="top: {{ $pct($nowM) }}%"></span>@endif
                            @endif
                            @foreach ($day['timed'] as $e)
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
