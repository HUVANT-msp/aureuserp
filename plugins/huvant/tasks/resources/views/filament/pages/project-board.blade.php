<x-filament-panels::page>
    @livewire('huvant-task-board', ['projectId' => (int) $this->record->getKey()])
    @livewire('huvant-task-panel')
</x-filament-panels::page>
