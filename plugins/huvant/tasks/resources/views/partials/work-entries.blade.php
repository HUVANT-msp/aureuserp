@php use Huvant\Tasks\Support\Palette; use Huvant\Tasks\Support\TaskWork; @endphp
@if ($entries->isNotEmpty())
    <ol class="hv-entries">
        @foreach ($entries as $entry)
            <li>
                <span class="hv-avatar small" style="--ac: {{ Palette::person((int) $entry->user_id) }}" title="{{ $entry->user?->name }}">{{ Palette::initials($entry->user?->name) }}</span>
                <div class="hv-grow">
                    <p>{{ $entry->name ?: '—' }}</p>
                    <small>
                        {{ $entry->user?->name ?? '—' }} · {{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}
                        @if ((int) $entry->task_id !== (int) $task->id) · <em>{{ $entry->task?->title }}</em>@endif
                    </small>
                </div>
                <b>{{ TaskWork::hours((float) $entry->unit_amount) }}</b>
            </li>
        @endforeach
    </ol>
@endif
