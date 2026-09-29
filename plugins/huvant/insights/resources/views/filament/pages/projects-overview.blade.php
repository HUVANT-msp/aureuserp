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
            <div class="hv-ins-seg" role="tablist" aria-label="Period">
                @foreach ([7 => 'Last 7 days', 14 => '14 days', 30 => '30 days'] as $d => $label)
                    <button type="button" role="tab" aria-selected="{{ $days === $d ? 'true' : 'false' }}" wire:click="setDays({{ $d }})">{{ $label }}</button>
                @endforeach
            </div>
            <p class="hv-ins-muted">{{ $data['from']->format('d/m') }} – {{ $data['to']->format('d/m/Y') }}, compared with the previous {{ $days }} days</p>
        </div>

        <div class="hv-ins-kpis">
            <div><span>Time logged</span><b>{{ Worklog::format($data['hours']) }}</b></div>
            <div><span>Active projects</span><b>{{ $data['active'] }}</b></div>
            <div><span>Tasks done</span><b>{{ $data['done'] }}</b></div>
            <div><span>People working</span><b>{{ collect($data['leaders'])->where('hours', '>', 0)->count() }}</b></div>
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
                                    <span class="hv-ins-trend up">New</span>
                                @elseif ($project['trend'] > 0)
                                    <span class="hv-ins-trend up">▲ {{ $project['trend'] }}%</span>
                                @elseif ($project['trend'] < 0)
                                    <span class="hv-ins-trend down">▼ {{ abs($project['trend']) }}%</span>
                                @endif
                            </header>
                            <div class="hv-ins-meter"><span style="width: {{ round($project['hours'] / $maxHours * 100, 1) }}%"></span></div>
                            <div class="hv-ins-facts">
                                <span><b>{{ Worklog::format($project['hours']) }}</b> h</span>
                                <span><b>{{ $project['done'] }}</b> done</span>
                                <span><b>{{ $project['open'] }}</b> open @if ($project['overdue'])<em>· {{ $project['overdue'] }} overdue</em>@endif</span>
                                <span class="hv-ins-progress" title="Tasks done out of all tasks"><i style="width: {{ round($project['progress'] * 0.6, 1) }}px"></i>{{ $project['progress'] }}%</span>
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
                                <p class="hv-ins-muted small">No activity in this period.</p>
                            @endif
                        </div>
                        <svg class="hv-ins-spark" viewBox="0 0 120 28" preserveAspectRatio="none" aria-hidden="true">
                            <polyline points="{{ Insights::sparkline($project['curve']) }}" />
                        </svg>
                    </li>
                @empty
                    <li class="hv-ins-muted">No visible projects.</li>
                @endforelse
            </ol>

            <aside class="hv-ins-side">
                <h3>Who is working the most</h3>
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
                    <p class="hv-ins-muted">No time logged in this period.</p>
                @endforelse
                <p class="hv-ins-muted small">Sorted by hours, then by tasks done (✓). A task counts as done when it moved to Done in the period.</p>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
