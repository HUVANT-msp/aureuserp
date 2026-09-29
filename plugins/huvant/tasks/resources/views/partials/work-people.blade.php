@php use Huvant\Tasks\Support\Palette; use Huvant\Tasks\Support\TaskWork; @endphp
@if (empty($work['people']))
    <p class="hv-muted small">Ancora nessuna ora registrata.</p>
@else
    <div class="hv-split" role="img" aria-label="Divisione del lavoro">
        @foreach ($work['people'] as $person)
            <span style="flex: {{ max(0.5, $person['share']) }}; --ac: {{ Palette::person($person['id']) }}" title="{{ $person['name'] }} · {{ TaskWork::hours($person['hours']) }} h"></span>
        @endforeach
    </div>
    <ul class="hv-people-hours">
        @foreach ($work['people'] as $person)
            <li>
                <span class="hv-avatar small" style="--ac: {{ Palette::person($person['id']) }}">{{ Palette::initials($person['name']) }}</span>
                <span class="hv-grow">{{ $person['name'] }}</span>
                <span class="hv-muted">{{ $person['entries'] }} {{ $person['entries'] === 1 ? 'voce' : 'voci' }}</span>
                <b>{{ TaskWork::hours($person['hours']) }} h</b>
                <span class="hv-share">{{ number_format($person['share'], 0) }}%</span>
            </li>
        @endforeach
    </ul>
@endif
