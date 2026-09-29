@php
    use Huvant\Insights\Support\Insights;
    use Huvant\Worklog\Support\Worklog;
    $initials = fn (string $n) => collect(preg_split('/\s+/', trim($n)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $personColor = fn (int $id) => class_exists(\Huvant\Tasks\Support\Palette::class) ? \Huvant\Tasks\Support\Palette::person($id) : '#8b8782';
    $maxHours = max(0.01, collect($data['projects'])->max('hours'));
    $maxLeader = max(0.01, collect($data['leaders'])->max('hours'));
@endphp
<x-filament-panels::page>
    <div class="hv-ins">
        <div class="hv-ins-bar">
            <div class="hv-ins-seg" role="tablist" aria-label="Periodo">
                @foreach ([7 => 'Ultimi 7 giorni', 14 => '14 giorni', 30 => '30 giorni'] as $d => $label)
                    <button type="button" role="tab" aria-selected="{{ $days === $d ? 'true' : 'false' }}" wire:click="setDays({{ $d }})">{{ $label }}</button>
                @endforeach
            </div>
            <p class="hv-ins-muted">{{ $data['from']->format('d/m') }} – {{ $data['to']->format('d/m/Y') }}, confronto con i {{ $days }} giorni precedenti</p>
        </div>

        <div class="hv-ins-kpis">
            <div><span>Ore registrate</span><b>{{ Worklog::format($data['hours']) }}</b></div>
            <div><span>Progetti attivi</span><b>{{ $data['active'] }}</b></div>
            <div><span>Task completati</span><b>{{ $data['done'] }}</b></div>
            <div><span>Persone al lavoro</span><b>{{ collect($data['leaders'])->where('hours', '>', 0)->count() }}</b></div>
        </div>

        <div class="hv-ins-grid">
            <ol class="hv-ins-projects">
                @forelse ($data['projects'] as $i => $project)
                    <li class="hv-ins-project" style="--pc: {{ $project['color'] }}" wire:key="p-{{ $project['id'] }}">
                        <span class="hv-ins-rank">{{ $i + 1 }}</span>
                        <div class="hv-ins-main">
                            <header>
                                <a href="{{ rescue(fn () => \Webkul\Project\Filament\Resources\ProjectResource::getUrl('board', ['record' => $project['id']]), \Webkul\Project\Filament\Resources\ProjectResource::getUrl('view', ['record' => $project['id']]), false) }}">
                                    <span class="hv-ins-dot"></span>{{ $project['name'] }}
                                </a>
                                @if ($project['trend'] === null)
                                    <span class="hv-ins-trend up">Nuovo</span>
                                @elseif ($project['trend'] > 0)
                                    <span class="hv-ins-trend up">▲ {{ $project['trend'] }}%</span>
                                @elseif ($project['trend'] < 0)
                                    <span class="hv-ins-trend down">▼ {{ abs($project['trend']) }}%</span>
                                @endif
                            </header>
                            <div class="hv-ins-meter"><span style="width: {{ round($project['hours'] / $maxHours * 100, 1) }}%"></span></div>
                            <div class="hv-ins-facts">
                                <span><b>{{ Worklog::format($project['hours']) }}</b> h</span>
                                <span><b>{{ $project['done'] }}</b> completati</span>
                                <span><b>{{ $project['open'] }}</b> aperti @if ($project['overdue'])<em>· {{ $project['overdue'] }} scaduti</em>@endif</span>
                                <span class="hv-ins-progress" title="Task completati sul totale"><i style="width: {{ round($project['progress'] * 0.6, 1) }}px"></i>{{ $project['progress'] }}%</span>
                            </div>
                            @if ($project['people'])
                                <ul class="hv-ins-people">
                                    @foreach (array_slice($project['people'], 0, 5) as $person)
                                        <li title="{{ $person['name'] }}">
                                            <span class="hv-ins-avatar" style="--ac: {{ $personColor($person['id']) }}">{{ $initials($person['name']) }}</span>
                                            <span class="hv-ins-name">{{ $person['name'] }}</span>
                                            @if ($person['hours'] > 0)<b>{{ Worklog::format($person['hours']) }}</b>@endif
                                            @if ($person['done'] > 0)<span class="hv-ins-done">✓ {{ $person['done'] }}</span>@endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="hv-ins-muted small">Nessuna attività nel periodo.</p>
                            @endif
                        </div>
                        <svg class="hv-ins-spark" viewBox="0 0 120 28" preserveAspectRatio="none" aria-hidden="true">
                            <polyline points="{{ Insights::sparkline($project['curve']) }}" />
                        </svg>
                    </li>
                @empty
                    <li class="hv-ins-muted">Nessun progetto visibile.</li>
                @endforelse
            </ol>

            <aside class="hv-ins-side">
                <h3>Chi sta lavorando di più</h3>
                @forelse ($data['leaders'] as $person)
                    <div class="hv-ins-leader">
                        <span class="hv-ins-avatar" style="--ac: {{ $personColor($person['id']) }}">{{ $initials($person['name']) }}</span>
                        <div>
                            <p>{{ $person['name'] }}</p>
                            <div class="hv-ins-meter thin"><span style="width: {{ round($person['hours'] / $maxLeader * 100, 1) }}%"></span></div>
                        </div>
                        <span class="hv-ins-leader-num"><b>{{ Worklog::format($person['hours']) }}</b><small>✓ {{ $person['done'] }}</small></span>
                    </div>
                @empty
                    <p class="hv-ins-muted">Nessuna ora registrata nel periodo.</p>
                @endforelse
                <p class="hv-ins-muted small">Ordinati per ore, poi per task completati (✓). Un task conta come completato quando è passato a «Completati» nel periodo.</p>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
