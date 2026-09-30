@php
    $origin = class_exists(\Huvant\Tasks\Support\TaskOrigin::class) ? \Huvant\Tasks\Support\TaskOrigin::for($this->record) : null;
@endphp
<x-filament-panels::page>
    <p class="hv-doc-hint">
        @if ($this->record->project_id)
            Visible to everyone on the project; they also appear in the project documents, under “Task attachments”.
        @else
            This task is not in a project: its documents are visible to whoever can see the task.
        @endif
    </p>
    @if ($origin)
        <div class="hv-doc-origin">
            <x-filament::icon :icon="$origin['source'] === 'canvas' ? 'heroicon-o-presentation-chart-bar' : 'heroicon-o-document-text'" class="hv-doc-origin-icon" />
            <div>
                <b>{{ $origin['source'] === 'canvas' ? 'Canvas' : 'Meeting minutes' }} · {{ $origin['title'] }}</b>
                <small>{{ $origin['date'] ? \Carbon\Carbon::parse($origin['date'])->format('j M Y') : '' }} · the meeting this task came from</small>
            </div>
            @if ($origin['pdf'] ?? false)
                <x-filament::button tag="a" size="sm" icon="heroicon-m-arrow-down-tray" :href="\Huvant\Tasks\Support\TaskOrigin::downloadUrl($this->record)">Download PDF</x-filament::button>
            @elseif ($origin['url'])
                <x-filament::button tag="a" size="sm" color="gray" icon="heroicon-m-arrow-top-right-on-square" :href="$origin['url']">Open</x-filament::button>
            @endif
        </div>
    @endif
    {{ $this->table }}
</x-filament-panels::page>
