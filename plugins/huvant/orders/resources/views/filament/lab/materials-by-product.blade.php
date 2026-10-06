@php use Huvant\Orders\Support\LabInventory; @endphp
<x-filament-panels::page>
    <div class="hv-lab-legend">
        <span><i class="hv-lab-dot hv-lab-ok"></i> {{ __('huvant-orders::lab.enough') }}</span>
        <span><i class="hv-lab-dot hv-lab-low"></i> {{ __('huvant-orders::lab.close_to_minimum') }}</span>
        <span><i class="hv-lab-dot hv-lab-below"></i> {{ __('huvant-orders::lab.below_minimum') }}</span>
        <span><i class="hv-lab-dot hv-lab-unset"></i> {{ __('huvant-orders::lab.no_minimum') }}</span>
        <span>- - - {{ __('huvant-orders::lab.minimum') }}</span>
    </div>

    @if ($withRecipe->isEmpty())
        <x-filament::section>
            <p class="hv-lab-empty">{{ __('huvant-orders::lab.no_recipes') }}</p>
        </x-filament::section>
    @else
        <div class="hv-lab-products">
            @foreach ($withRecipe as $item)
                <section class="hv-lab-card" wire:key="product-{{ $item['product']->id }}">
                    <div class="hv-lab-card-head">
                        <span class="hv-lab-card-title">{{ $item['product']->name }}</span>
                        @if ($item['pieces'] !== null)
                            <span class="hv-lab-card-meta">{{ __('huvant-orders::lab.enough_for', ['count' => $item['pieces'], 'unit' => __($item['pieces'] === 1 ? 'huvant-orders::lab.piece' : 'huvant-orders::lab.pieces_lower')]) }}</span>
                        @endif
                    </div>
                    <div class="hv-lab-histogram">
                        @foreach ($item['columns'] as $column)
                            <div class="hv-lab-column hv-lab-{{ $column['level'] }}" title="{{ __('huvant-orders::lab.material_chart_title', ['name' => $column['name'], 'total' => LabInventory::format($column['total'], $column['unit']), 'minimum' => $column['minimum'] > 0 ? __('huvant-orders::lab.minimum_suffix', ['minimum' => LabInventory::format($column['minimum'], $column['unit'])]) : '']) }}">
                                <div class="hv-lab-column-plot">
                                    <div class="hv-lab-column-bar" style="height: {{ $column['height'] }}%"></div>
                                    @if ($column['min_at'] !== null)
                                        <div class="hv-lab-column-min" style="bottom: {{ $column['min_at'] }}%"></div>
                                    @endif
                                </div>
                                <div class="hv-lab-column-name">{{ $column['name'] }}</div>
                                <div class="hv-lab-column-value">
                                    {{ LabInventory::format($column['total'], $column['unit']) }}<br>
                                    {{ __('huvant-orders::lab.per_piece_lower', ['quantity' => LabInventory::format($column['per_unit'], $column['unit'])]) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    @if ($withoutRecipe->isNotEmpty())
        <p class="hv-lab-empty">{{ __('huvant-orders::lab.without_recipe', ['products' => $withoutRecipe->pluck('name')->implode(', ')]) }}</p>
    @endif
</x-filament-panels::page>
