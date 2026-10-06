<x-filament-panels::page>
    <div class="hv-manage-summary">
        <div>
            <strong>{{ $record->partner?->name ?? __('huvant-orders::manufacturing.stock_order') }}</strong>
            <span>{{ __('huvant-orders::manufacturing.entry_progress', ['managed' => $entries->filter->isManaged()->count(), 'total' => $entries->count()]) }}</span>
        </div>
        @if ($record->expected_delivery_date)
            <span class="hv-manage-deadline">
                <x-filament::icon icon="heroicon-o-calendar-days" class="h-4 w-4" />
                {{ __('huvant-orders::manufacturing.due_on', ['date' => $record->expected_delivery_date->format('d/m/Y')]) }}
            </span>
        @endif
        @if ($record->manufacturing_managed_at)
            <span @class(['hv-state-badge', 'is-complete' => $isComplete])>
                {{ $isComplete ? __('huvant-orders::manufacturing.completed') : __('huvant-orders::manufacturing.in_progress') }}
            </span>
            <x-filament::button tag="a" :href="$this->reportUrl()" target="_blank" color="gray" icon="heroicon-o-arrow-down-tray">
                {{ __('huvant-orders::manufacturing.download_report') }}
            </x-filament::button>
        @endif
    </div>

    <div class="hv-entry-matrix" role="table" aria-label="{{ __('huvant-orders::manufacturing.product_entries') }}">
        <div class="hv-entry-row hv-entry-head" role="row">
            <span role="columnheader">#</span>
            <span role="columnheader">{{ __('huvant-orders::manufacturing.product') }}</span>
            <span role="columnheader">{{ __('huvant-orders::manufacturing.source') }}</span>
            <span role="columnheader">{{ __('huvant-orders::manufacturing.traceability') }}</span>
            <span role="columnheader">{{ __('huvant-orders::manufacturing.actions') }}</span>
        </div>

        @forelse ($entries as $entry)
            <div class="hv-entry-row" role="row" wire:key="manufacturing-entry-{{ $entry->id }}">
                <span class="hv-entry-number" role="cell">{{ $entry->position }}</span>
                <div class="hv-entry-product" role="cell">
                    <strong>{{ $entry->product->name }}</strong>
                    <span>{{ $entry->product->reference ?: __('huvant-orders::manufacturing.no_reference') }}</span>
                </div>
                <div class="hv-entry-source" role="cell">
                    @if ($entry->source)
                        <span class="hv-source-badge is-{{ $entry->source->value }}">{{ $entry->source->getLabel() }}</span>
                        @if ($entry->source->value === 'production' && $entry->productionTask)
                            <small>
                                {{ $entry->productionTask->projectTask?->users->pluck('name')->join(', ') ?: '—' }}
                                · {{ $entry->productionTask->due_date?->format('d/m/Y') }}
                            </small>
                        @endif
                    @else
                        <span class="hv-source-empty">{{ __('huvant-orders::manufacturing.not_managed') }}</span>
                    @endif
                </div>
                <div class="hv-entry-trace" role="cell">
                    @if ($entry->productUnit)
                        <strong>{{ $entry->productUnit->code }}</strong>
                        <span>{{ __('huvant-orders::manufacturing.expires_short', ['date' => $entry->productUnit->expiry_date?->format('d/m/Y') ?? '—']) }}</span>
                        <button type="button" class="hv-material-link" wire:click="openMaterials({{ $entry->productUnit->id }})">
                            {{ __('huvant-orders::lab.materials') }}
                        </button>
                    @elseif ($entry->productionTask)
                        <strong>{{ $entry->productionTask->status->getLabel() }}</strong>
                        <span>{{ $entry->productionTask->projectTask?->project?->name }}</span>
                    @else
                        <span>—</span>
                    @endif
                </div>
                <div class="hv-entry-actions" role="cell">
                    @if (! $record->manufacturing_managed_at)
                        <x-filament::button
                            size="sm"
                            color="success"
                            icon="heroicon-o-archive-box-arrow-down"
                            wire:click="openStock({{ $entry->id }})"
                            :disabled="! $entry->product_unit_id && (int) ($availableByProduct[$entry->product_id] ?? 0) === 0"
                        >
                            {{ __('huvant-orders::manufacturing.take_from_stock') }}
                        </x-filament::button>
                        <x-filament::button
                            size="sm"
                            color="warning"
                            icon="heroicon-o-cog-6-tooth"
                            wire:click="openProduction({{ $entry->id }})"
                        >
                            {{ __('huvant-orders::manufacturing.send_to_production') }}
                        </x-filament::button>
                    @else
                        <span @class(['hv-entry-completion', 'is-complete' => $entry->isComplete()])>
                            <x-filament::icon :icon="$entry->isComplete() ? 'heroicon-s-check-circle' : 'heroicon-o-clock'" class="h-4 w-4" />
                            {{ $entry->isComplete() ? __('huvant-orders::manufacturing.completed') : __('huvant-orders::manufacturing.in_progress') }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="hv-offer-empty">
                <x-filament::icon icon="heroicon-o-information-circle" class="h-7 w-7" />
                <span>{{ __('huvant-orders::manufacturing.no_manufactured_products_help') }}</span>
            </div>
        @endforelse
    </div>

    @if (! $record->manufacturing_managed_at)
        <div class="hv-manage-footer">
            <p>{{ $allManaged ? __('huvant-orders::manufacturing.ready_to_finish') : __('huvant-orders::manufacturing.finish_after_every_entry') }}</p>
            <x-filament::button wire:click="done" icon="heroicon-o-check" :disabled="! $allManaged">
                {{ __('huvant-orders::manufacturing.done') }}
            </x-filament::button>
        </div>
    @endif

    <x-filament::modal id="hv-stock-choice" width="5xl" :close-by-clicking-away="false">
        <x-slot name="heading">{{ __('huvant-orders::manufacturing.choose_stock_unit') }}</x-slot>
        <x-slot name="description">
            {{ $stockEntry?->product?->name }} · {{ __('huvant-orders::manufacturing.available_now', ['count' => $stockUnits->count()]) }}
        </x-slot>

        <div class="hv-stock-picker">
            @forelse ($stockUnits as $unit)
                <label class="hv-stock-unit" for="stock-unit-{{ $unit->id }}">
                    <input id="stock-unit-{{ $unit->id }}" type="radio" wire:model="selectedUnitId" value="{{ $unit->id }}" />
                    <span class="hv-stock-code">{{ $unit->code }}</span>
                    <span>{{ $unit->production_date?->format('d/m/Y') }}</span>
                    <span>{{ $unit->expiry_date?->format('d/m/Y') ?? '—' }}</span>
                    <span>{{ $unit->creator?->name ?? '—' }}</span>
                    <span class="hv-quality" aria-label="{{ __('huvant-orders::manufacturing.quality_rating', ['rating' => $unit->quality_rating ?? 0]) }}">
                        @foreach (range(1, 5) as $star)
                            <x-filament::icon :icon="$star <= ($unit->quality_rating ?? 0) ? 'heroicon-s-star' : 'heroicon-o-star'" class="h-4 w-4" />
                        @endforeach
                    </span>
                    @if ($unit->notes)
                        <span class="hv-unit-notes" tabindex="0" data-notes="{{ $unit->notes }}">
                            <x-filament::icon icon="heroicon-o-document-text" class="h-4 w-4" />
                            {{ __('huvant-orders::manufacturing.notes') }}
                        </span>
                    @else
                        <span>—</span>
                    @endif
                </label>
            @empty
                <div class="hv-offer-empty">{{ __('huvant-orders::manufacturing.no_stock_units') }}</div>
            @endforelse
        </div>

        @error('selectedUnitId') <p class="hv-form-error">{{ $message }}</p> @enderror
        <x-slot name="footerActions">
            <x-filament::button wire:click="chooseStock" icon="heroicon-o-check" :disabled="! $selectedUnitId">
                {{ __('huvant-orders::manufacturing.use_selected_unit') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <x-filament::modal id="hv-production-choice" width="2xl" :close-by-clicking-away="false">
        <x-slot name="heading">{{ __('huvant-orders::manufacturing.send_to_production') }}</x-slot>
        <x-slot name="description">{{ $productionEntry?->product?->name }}</x-slot>

        <div class="hv-production-form">
            <fieldset>
                <legend>{{ __('huvant-orders::manufacturing.assignees') }}</legend>
                <div class="hv-assignee-list">
                    @foreach ($labUsers as $person)
                        <label>
                            <input type="checkbox" wire:model="productionAssignees" value="{{ $person->id }}" />
                            <span>{{ $person->name }}</span>
                        </label>
                    @endforeach
                </div>
                @if ($labUsers->isEmpty())
                    <p class="hv-form-error">{{ __('huvant-orders::manufacturing.no_lab_members') }}</p>
                @endif
                @error('productionAssignees') <p class="hv-form-error">{{ $message }}</p> @enderror
            </fieldset>

            <label class="hv-production-date">
                <span>{{ __('huvant-orders::manufacturing.production_deadline') }}</span>
                <input
                    type="date"
                    wire:model="productionDeadline"
                    @if (! $record->expected_delivery_date?->isBefore(today())) min="{{ today()->toDateString() }}" @endif
                    @if ($record->expected_delivery_date) max="{{ $record->expected_delivery_date->toDateString() }}" @endif
                    required
                />
                <small>{{ __('huvant-orders::manufacturing.production_deadline_help') }}</small>
            </label>
            @error('productionDeadline') <p class="hv-form-error">{{ $message }}</p> @enderror
        </div>

        <x-slot name="footerActions">
            <x-filament::button wire:click="chooseProduction" icon="heroicon-o-check" :disabled="$labUsers->isEmpty()">
                {{ __('huvant-orders::manufacturing.done') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    <x-filament::modal id="hv-unit-materials" width="3xl">
        <x-slot name="heading">{{ $materialsUnit?->code }}</x-slot>
        <x-slot name="description">{{ $materialsUnit?->product?->name }}</x-slot>
        @if ($materialsUnit)
            @include('huvant-orders::filament.lab.unit-materials', ['materials' => $materialsUnit->materials])
        @endif
    </x-filament::modal>
</x-filament-panels::page>
