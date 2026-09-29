@php
    use Huvant\Worklog\Support\Worklog;
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
            <div class="hv-wl-legend">
                <span class="hv-wl-chip hv-wl-missing">Nessuna ora</span>
                <span class="hv-wl-chip hv-wl-short">Sotto le previste</span>
            </div>
        </div>

        <div class="hv-wl-tablewrap">
            <table class="hv-wl-table">
                <thead>
                    <tr>
                        <th class="hv-wl-taskcol">Persona</th>
                        @foreach ($data['days'] as $i => $day)
                            <th @class(['hv-wl-today' => $day === $today, 'hv-wl-weekend' => $i >= 5])>
                                {{ $dayNames[$i] }} <span>{{ \Carbon\CarbonImmutable::parse($day)->format('j') }}</span>
                            </th>
                        @endforeach
                        <th>Totale</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['rows'] as $row)
                        <tr>
                            <td class="hv-wl-taskcol"><span class="hv-wl-title">{{ $row['name'] }}</span></td>
                            @foreach ($row['cells'] as $i => $cell)
                                @php($past = $cell['day'] < $today)
                                <td @class([
                                    'hv-wl-num',
                                    'hv-wl-today' => $cell['day'] === $today,
                                    'hv-wl-weekend' => $i >= 5,
                                    'hv-wl-missing' => $past && $cell['expected'] > 0 && $cell['hours'] == 0,
                                    'hv-wl-short' => $past && $cell['expected'] > 0 && $cell['hours'] > 0 && $cell['hours'] < $cell['expected'],
                                ]) title="Previste {{ Worklog::format($cell['expected']) }} h">
                                    {{ $cell['hours'] > 0 ? Worklog::format($cell['hours']) : '–' }}
                                </td>
                            @endforeach
                            <td class="hv-wl-total">{{ Worklog::format($row['total']) }}<small>/ {{ Worklog::format($row['expected']) }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="hv-wl-emptyrow">Nessun dipendente collegato a un utente.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="hv-wl-heading">Ore per progetto</h2>
        @if (empty($data['projects']))
            <p class="hv-wl-muted">Nessuna ora registrata in questa settimana.</p>
        @else
            @php($max = max(array_column($data['projects'], 'hours')))
            <ul class="hv-wl-bars">
                @foreach ($data['projects'] as $project)
                    <li>
                        <span class="hv-wl-bar-label">{{ $project['project'] }}</span>
                        <span class="hv-wl-bar"><span style="width: {{ $max > 0 ? round($project['hours'] / $max * 100, 1) : 0 }}%"></span></span>
                        <span class="hv-wl-bar-value">{{ Worklog::format($project['hours']) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-filament-panels::page>
