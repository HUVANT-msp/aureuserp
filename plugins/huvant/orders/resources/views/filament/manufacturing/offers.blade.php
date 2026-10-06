<x-filament-panels::page>
    <div class="hv-offer-board">
        @foreach ([['key' => 'to_manage', 'orders' => $toManage], ['key' => 'managed', 'orders' => $managed]] as $column)
            <section class="hv-offer-column" aria-labelledby="hv-{{ $column['key'] }}-title">
                <header class="hv-offer-column-head">
                    <div>
                        <h2 id="hv-{{ $column['key'] }}-title">{{ __('huvant-orders::manufacturing.'.$column['key']) }}</h2>
                        <p>{{ __('huvant-orders::manufacturing.'.$column['key'].'_help') }}</p>
                    </div>
                    <span class="hv-offer-count">{{ $column['orders']->count() }}</span>
                </header>

                <div class="hv-offer-stack">
                    @forelse ($column['orders'] as $order)
                        <a href="{{ $this->manageUrl($order) }}" class="hv-offer-card" wire:key="offer-{{ $order->id }}">
                            <div class="hv-offer-card-head">
                                <div>
                                    <strong>{{ $order->order_number }}</strong>
                                    <span>{{ $order->partner?->name ?? __('huvant-orders::manufacturing.stock_order') }}</span>
                                </div>
                                <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5" />
                            </div>

                            <ul class="hv-offer-products">
                                @foreach ($order->lines as $line)
                                    <li><span>{{ $line->product?->name ?? $line->description }}</span><strong>× {{ (float) $line->quantity }}</strong></li>
                                @endforeach
                            </ul>

                            <div class="hv-offer-meta">
                                <span>{{ __('huvant-orders::manufacturing.confirmed_on', ['date' => $order->confirmed_at?->format('d/m/Y')]) }}</span>
                                @if ($order->expected_delivery_date)
                                    <span>{{ __('huvant-orders::manufacturing.due_on', ['date' => $order->expected_delivery_date->format('d/m/Y')]) }}</span>
                                @endif
                                @if ($order->manufacturing_managed_at)
                                    <span>{{ __('huvant-orders::manufacturing.managed_by', ['name' => $order->manufacturingManagedBy?->name ?? '—']) }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="hv-offer-empty">
                            <x-filament::icon icon="{{ $column['key'] === 'to_manage' ? 'heroicon-o-check-circle' : 'heroicon-o-inbox' }}" class="h-7 w-7" />
                            <span>{{ __('huvant-orders::manufacturing.no_'.$column['key']) }}</span>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
