<x-filament-panels::page>
    @if ($products->isEmpty())
        <x-filament::section>
            <p class="hv-lab-empty">{{ __('huvant-orders::lab.no_products') }}</p>
        </x-filament::section>
    @else
        <div class="hv-lab-cards">
            @foreach ($products as $item)
                <a class="hv-lab-card" href="{{ $item['url'] }}" wire:key="stock-{{ $item['product']->id }}">
                    <div>
                        <div class="hv-lab-card-title">{{ $item['product']->name }}</div>
                        <div class="hv-lab-prefix">{{ $item['prefix'] }}-…</div>
                    </div>
                    <div class="hv-lab-stats">
                        <span>{{ __('huvant-orders::lab.in_lab_count', ['count' => $item['counts']['in_lab']]) }}</span>
                        <span>{{ __('huvant-orders::lab.out_count', ['count' => $item['counts']['out']]) }}</span>
                        <span>{{ __('huvant-orders::lab.sold_count', ['count' => $item['counts']['sold']]) }}</span>
                    </div>
                    <div class="hv-lab-card-meta">
                        {{ $item['last'] ? __('huvant-orders::lab.last_made', ['date' => \Illuminate\Support\Carbon::parse($item['last'])->format('d/m/Y')]) : __('huvant-orders::lab.never_made') }}
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
