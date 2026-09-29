<x-filament-panels::page>
    <nav class="hv-doc-trail" aria-label="Path">
        <button type="button" wire:click="openFolder(null)" @class(['is-current' => ! $trail && ! $taskFiles])>
            <x-filament::icon icon="heroicon-m-folder" class="h-4 w-4" />
            Documents
        </button>
        @foreach ($trail as $crumb)
            <x-filament::icon icon="heroicon-m-chevron-right" class="hv-doc-sep h-4 w-4" />
            <button type="button" wire:click="openFolder('{{ $crumb->id }}')" @class(['is-current' => $loop->last])>{{ $crumb->name }}</button>
        @endforeach
        @if ($taskFiles)
            <x-filament::icon icon="heroicon-m-chevron-right" class="hv-doc-sep h-4 w-4" />
            <button type="button" class="is-current">Task attachments</button>
        @endif
    </nav>

    @if ($folders->isNotEmpty() || $taskCount > 0)
        <ul class="hv-doc-folders">
            @foreach ($folders as $folder)
                <li wire:key="folder-{{ $folder->id }}">
                    <button type="button" class="hv-doc-folder" wire:click="openFolder('{{ $folder->id }}')">
                        <x-filament::icon icon="heroicon-s-folder" class="hv-doc-folder-icon" />
                        <span class="hv-doc-folder-name">{{ $folder->name }}</span>
                        <span class="hv-doc-folder-meta">
                            @php($items = $folder->documents_count + $folder->children_count)
                            {{ $items === 0 ? 'Empty' : ($items === 1 ? '1 item' : $items.' items') }}
                        </span>
                    </button>
                    <div class="hv-doc-folder-actions">
                        {{ ($this->renameFolderAction)(['folder' => $folder->id]) }}
                        {{ ($this->deleteFolderAction)(['folder' => $folder->id]) }}
                    </div>
                </li>
            @endforeach
            @if ($taskCount > 0)
                <li>
                    <button type="button" class="hv-doc-folder is-virtual" wire:click="openFolder('task')">
                        <x-filament::icon icon="heroicon-s-paper-clip" class="hv-doc-folder-icon" />
                        <span class="hv-doc-folder-name">Task attachments</span>
                        <span class="hv-doc-folder-meta">{{ $taskCount === 1 ? '1 item' : $taskCount.' items' }}</span>
                    </button>
                </li>
            @endif
        </ul>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
