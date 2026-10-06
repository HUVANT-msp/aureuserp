<x-filament-panels::page>
    @if ($record->manufacturing_managed_at)
        <x-filament::section>
            <x-slot name="heading">{{ __('huvant-orders::manufacturing.managed') }}</x-slot>
            <x-slot name="description">{{ __('huvant-orders::manufacturing.managed_summary') }}</x-slot>

            <div class="hv-allocation-list">
                @forelse ($lines as $item)
                    <div class="hv-allocation-row">
                        <div>
                            <strong>{{ $item['line']->product->name }}</strong>
                            <span>{{ __('huvant-orders::manufacturing.requested_pieces', ['count' => $item['requested']]) }}</span>
                        </div>
                        <div class="hv-allocation-result">
                            <span class="is-stock">{{ __('huvant-orders::manufacturing.from_stock_count', ['count' => $item['line']->stock_quantity]) }}</span>
                            <span class="is-production">{{ __('huvant-orders::manufacturing.to_produce_count', ['count' => $item['line']->production_quantity]) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="hv-lab-empty">{{ __('huvant-orders::manufacturing.no_manufactured_products') }}</p>
                @endforelse
            </div>
        </x-filament::section>
    @else
        <form wire:submit="manage">
            <x-filament::section>
                <x-slot name="heading">{{ __('huvant-orders::manufacturing.choose_source') }}</x-slot>
                <x-slot name="description">{{ __('huvant-orders::manufacturing.choose_source_help') }}</x-slot>

                <div class="hv-allocation-list">
                    @forelse ($lines as $item)
                        @php($lineId = $item['line']->getKey())
                        <div class="hv-allocation-row" wire:key="allocation-{{ $lineId }}">
                            <div class="hv-allocation-product">
                                <strong>{{ $item['line']->product->name }}</strong>
                                <span>{{ __('huvant-orders::manufacturing.requested_pieces', ['count' => $item['requested']]) }}</span>
                            </div>

                            <div class="hv-allocation-control">
                                <label for="stock-{{ $lineId }}">
                                    <span>{{ __('huvant-orders::manufacturing.from_stock') }}</span>
                                    <small>{{ __('huvant-orders::manufacturing.available_now', ['count' => $item['available']]) }}</small>
                                </label>
                                <select id="stock-{{ $lineId }}" wire:model.live="stockQuantities.{{ $lineId }}">
                                    @for ($quantity = 0; $quantity <= min($item['requested'], $item['available']); $quantity++)
                                        <option value="{{ $quantity }}">{{ $quantity }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="hv-allocation-production">
                                <span>{{ __('huvant-orders::manufacturing.new_production') }}</span>
                                <strong>{{ max(0, $item['requested'] - (int) ($stockQuantities[$lineId] ?? 0)) }}</strong>
                            </div>
                        </div>
                    @empty
                        <div class="hv-offer-empty">
                            <x-filament::icon icon="heroicon-o-information-circle" class="h-7 w-7" />
                            <span>{{ __('huvant-orders::manufacturing.no_manufactured_products_help') }}</span>
                        </div>
                    @endforelse
                </div>

                <x-slot name="footerActions">
                    <x-filament::button type="submit" icon="heroicon-o-check">
                        {{ __('huvant-orders::manufacturing.confirm_management') }}
                    </x-filament::button>
                </x-slot>
            </x-filament::section>
        </form>
    @endif
</x-filament-panels::page>
