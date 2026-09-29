@php
    use Huvant\Worklog\Support\Worklog;
    $fmt = fn (float $h): string => $h > 0 ? Worklog::format($h) : '';
    $dayNames = ['Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom'];
@endphp
<x-filament-panels::page>
    <div class="hv-wl">
        <div class="hv-wl-toolbar">
            <div class="hv-wl-weeknav">
                <x-filament::icon-button icon="heroicon-m-chevron-left" wire:click="shiftWeek(-1)" label="Settimana precedente" color="gray" />
                <span class="hv-wl-range">{{ Worklog::weekLabel($monday) }}</span>
                <x-filament::icon-button icon="heroicon-m-chevron-right" wire:click="shiftWeek(1)" label="Settimana successiva" color="gray" />
                @if ($monday->toDateString() !== \Carbon\CarbonImmutable::today()->startOfWeek()->toDateString())
                    <x-filament::button size="sm" color="gray" wire:click="thisWeek">Oggi</x-filament::button>
                @endif
            </div>
            <x-filament::button size="sm" color="gray" icon="heroicon-m-user-plus" wire:click="$toggle('joining')">
                Aggiungimi a un task
            </x-filament::button>
        </div>

        @if ($joining)
            <div class="hv-wl-join">
                <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                    <x-filament::input type="search" wire:model.live.debounce.300ms="search" placeholder="Cerca un task dei tuoi progetti" autofocus />
                </x-filament::input.wrapper>
                <ul>
                    @forelse ($joinable as $task)
                        <li>
                            <span><strong>{{ $task->title }}</strong> <small>{{ $task->project?->name ?? 'Senza progetto' }}</small></span>
                            <x-filament::button size="xs" wire:click="join({{ $task->id }})">Aggiungimi</x-filament::button>
                        </li>
                    @empty
                        <li class="hv-wl-muted">Nessun task trovato.</li>
                    @endforelse
                </ul>
            </div>
        @endif

        <div class="hv-wl-tablewrap">
            <table class="hv-wl-table">
                <thead>
                    <tr>
                        <th class="hv-wl-taskcol">Task</th>
                        @foreach ($grid['days'] as $i => $day)
                            <th @class(['hv-wl-today' => $day === $today, 'hv-wl-weekend' => $i >= 5])>
                                {{ $dayNames[$i] }} <span>{{ \Carbon\CarbonImmutable::parse($day)->format('j') }}</span>
                            </th>
                        @endforeach
                        <th>Totale</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grid['rows'] as $row)
                        <tr wire:key="row-{{ $row['id'] }}">
                            <td class="hv-wl-taskcol">
                                <div class="hv-wl-task">
                                    <button type="button" class="hv-wl-play" wire:click="start({{ $row['id'] }})"
                                            title="Avvia il timer" @disabled($running && $running->task_id == $row['id'])>
                                        <x-filament::icon :icon="$running && $running->task_id == $row['id'] ? 'heroicon-m-clock' : 'heroicon-m-play'" class="h-4 w-4" />
                                    </button>
                                    <a href="{{ \Webkul\Project\Filament\Resources\TaskResource::getUrl('view', ['record' => $row['id']]) }}">
                                        <span class="hv-wl-title">{{ $row['title'] }}</span>
                                        <span class="hv-wl-project">{{ $row['project'] }}</span>
                                    </a>
                                </div>
                            </td>
                            @foreach ($grid['days'] as $i => $day)
                                <td @class(['hv-wl-today' => $day === $today, 'hv-wl-weekend' => $i >= 5])>
                                    <input type="text" inputmode="decimal" class="hv-wl-cell"
                                           wire:key="cell-{{ $row['id'] }}-{{ $day }}-{{ $row['hours'][$day] }}"
                                           value="{{ $fmt($row['hours'][$day]) }}" placeholder="–"
                                           aria-label="{{ $row['title'] }}, {{ $day }}"
                                           x-on:change="$wire.saveCell({{ $row['id'] }}, '{{ $day }}', $event.target.value)"
                                           x-on:keydown.enter="$event.target.blur()" />
                                </td>
                            @endforeach
                            <td class="hv-wl-total">{{ Worklog::format(array_sum($row['hours'])) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="hv-wl-emptyrow">
                                Non sei assegnatario di nessun task aperto. Usa «Aggiungimi a un task» per iniziare a registrare le ore.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="hv-wl-taskcol">Totale</th>
                        @foreach ($grid['days'] as $i => $day)
                            @php($logged = $grid['totals'][$day])
                            @php($expected = $grid['expected'][$day])
                            <td @class([
                                'hv-wl-today' => $day === $today,
                                'hv-wl-weekend' => $i >= 5,
                                'hv-wl-short' => $expected > 0 && $day < $today && $logged < $expected,
                            ]) title="Previste {{ Worklog::format($expected) }} h">
                                {{ Worklog::format($logged) }}<small>/ {{ Worklog::format($expected) }}</small>
                            </td>
                        @endforeach
                        <td class="hv-wl-total">
                            {{ Worklog::format(array_sum($grid['totals'])) }}<small>/ {{ Worklog::format(array_sum($grid['expected'])) }}</small>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="hv-wl-hint">Scrivi le ore come 1,5 o 1:30. Il timer aggiunge le sue voci al giorno in cui è partito.</p>
    </div>
</x-filament-panels::page>
