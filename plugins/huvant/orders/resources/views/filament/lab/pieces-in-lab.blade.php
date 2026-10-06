<x-filament-panels::page>
    @if ($products->isEmpty())
        <x-filament::section>
            <p class="hv-lab-empty">{{ __('huvant-orders::lab.no_products') }}</p>
        </x-filament::section>
    @else
        <div class="hv-lab-legend">
            <span><i class="hv-lab-square is-filled" style="width:12px;height:12px"></i> {{ __('huvant-orders::lab.in_the_lab') }}</span>
            <span><i class="hv-lab-square is-expiring" style="width:12px;height:12px"></i> {{ __('huvant-orders::lab.expiring_soon') }}</span>
        </div>
        <div class="hv-lab-cards">
            @foreach ($products as $item)
                <a class="hv-lab-card" href="{{ $item['url'] }}" wire:key="pieces-{{ $item['product']->id }}">
                    <div class="hv-lab-card-head">
                        <span class="hv-lab-card-title">{{ $item['product']->name }}</span>
                        <span class="hv-lab-card-meta"><strong>{{ $item['count'] }}</strong> {{ __($item['count'] === 1 ? 'huvant-orders::lab.piece' : 'huvant-orders::lab.pieces_lower') }}</span>
                    </div>
                    <div class="hv-lab-grid" role="img" aria-label="{{ __('huvant-orders::lab.pieces_in_lab_aria', ['count' => $item['count']]) }}">
                        @for ($i = 0; $i < $item['squares']; $i++)
                            <span @class([
                                'hv-lab-square',
                                'is-expiring' => $i < $item['expiring'],
                                'is-filled' => $i >= $item['expiring'] && $i < $item['count'],
                            ])></span>
                        @endfor
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
