<x-filament-panels::page>
    <p class="hv-doc-hint">
        @if ($this->record->project_id)
            Visibili a chi lavora al progetto; li trovi anche nei documenti del progetto, in «Allegati ai task».
        @else
            Questo task non è in un progetto: i documenti sono visibili a chi vede il task.
        @endif
    </p>
    {{ $this->table }}
</x-filament-panels::page>
