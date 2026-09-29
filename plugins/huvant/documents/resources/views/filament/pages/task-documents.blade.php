<x-filament-panels::page>
    <p class="hv-doc-hint">
        @if ($this->record->project_id)
            Visible to everyone on the project; they also appear in the project documents, under “Task attachments”.
        @else
            This task is not in a project: its documents are visible to whoever can see the task.
        @endif
    </p>
    {{ $this->table }}
</x-filament-panels::page>
