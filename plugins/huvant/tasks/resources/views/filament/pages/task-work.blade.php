@php use Huvant\Tasks\Support\Board; use Huvant\Tasks\Support\Palette; use Huvant\Tasks\Support\TaskWork; @endphp
<x-filament-panels::page>
    <div class="hv-work-page">
        <section class="hv-card-box">
            <header class="hv-box-head">
                <h3>Who did the work</h3>
                <span class="hv-big">{{ TaskWork::hours($work['total']) }} h</span>
            </header>
            @include('huvant-tasks::partials.work-people', ['work' => $work])
        </section>

        @if ($subs->isNotEmpty())
            <section class="hv-card-box">
                <header class="hv-box-head"><h3>Subtasks</h3></header>
                <table class="hv-list compact">
                    <thead><tr><th>Subtask</th><th>Stage</th><th>People</th><th class="hv-num">Hours</th></tr></thead>
                    <tbody>
                        @foreach ($subs as $row)
                            <tr>
                                <td><a href="{{ \Webkul\Project\Filament\Resources\TaskResource::getUrl('work', ['record' => $row['task']]) }}">{{ $row['task']->title }}</a></td>
                                <td><span class="hv-stage hv-stage-{{ Board::stageKind((string) $row['task']->stage?->name) }}">{{ $row['task']->stage ? Board::stageLabel($row['task']->stage->name) : '—' }}</span></td>
                                <td>
                                    @forelse ($row['work']['people'] as $person)
                                        <span class="hv-person small"><span class="hv-avatar small" style="--ac: {{ Palette::person($person['id']) }}">{{ Palette::initials($person['name']) }}</span>{{ $person['name'] }} <b>{{ TaskWork::hours($person['hours']) }}</b></span>
                                    @empty
                                        @forelse ($row['task']->users as $person)
                                            <span class="hv-person small"><span class="hv-avatar small" style="--ac: {{ Palette::person($person->id) }}">{{ Palette::initials($person->name) }}</span>{{ $person->name }}</span>
                                        @empty
                                            <span class="hv-muted">—</span>
                                        @endforelse
                                    @endforelse
                                </td>
                                <td class="hv-num">{{ $row['work']['total'] > 0 ? TaskWork::hours($row['work']['total']) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif

        <section class="hv-card-box">
            <header class="hv-box-head"><h3>What was done</h3><span class="hv-muted">{{ $work['entries']->count() }} entries</span></header>
            @if ($work['entries']->isEmpty())
                <p class="hv-muted">No time logged on this task yet.</p>
            @else
                @include('huvant-tasks::partials.work-entries', ['entries' => $work['entries'], 'task' => $task])
            @endif
        </section>
    </div>
</x-filament-panels::page>
