<x-filament-panels::page>
    <section class="hv-offer-list" aria-live="polite">
        <header class="hv-offer-list-head">
            <div>
                <h2>{{ __('huvant-orders::manufacturing.'.($activeTab === 'history' ? 'history' : 'to_manage')) }}</h2>
                <p>{{ __('huvant-orders::manufacturing.'.($activeTab === 'history' ? 'history_help' : 'to_manage_help')) }}</p>
            </div>

            <div class="hv-offer-tabs">
                <x-filament::tabs label="{{ __('huvant-orders::manufacturing.offer_views') }}">
                    <x-filament::tabs.item
                        :active="$activeTab === 'to_manage'"
                        icon="heroicon-o-inbox-stack"
                        wire:click="$set('activeTab', 'to_manage')"
                    >
                        {{ __('huvant-orders::manufacturing.to_manage') }}
                        <span class="hv-tab-count">{{ $toManageCount }}</span>
                    </x-filament::tabs.item>

                    <x-filament::tabs.item
                        :active="$activeTab === 'history'"
                        icon="heroicon-o-clock"
                        wire:click="$set('activeTab', 'history')"
                    >
                        {{ __('huvant-orders::manufacturing.history') }}
                        <span class="hv-tab-count">{{ $historyCount }}</span>
                    </x-filament::tabs.item>
                </x-filament::tabs>
            </div>
        </header>

        <div class="hv-offer-columns" aria-hidden="true">
            <span>{{ __('huvant-orders::manufacturing.offer') }}</span>
            <span>{{ __('huvant-orders::manufacturing.products') }}</span>
            <span>{{ __('huvant-orders::manufacturing.timing') }}</span>
            <span>{{ __('huvant-orders::manufacturing.status') }}</span>
        </div>

        <div class="hv-offer-stack">
            @forelse ($orders as $order)
                <a href="{{ $this->manageUrl($order) }}" class="hv-offer-card" wire:key="offer-{{ $order->id }}">
                    <div class="hv-offer-identity">
                        <strong>{{ $order->order_number }}</strong>
                        <span>{{ $order->partner?->name ?? __('huvant-orders::manufacturing.stock_order') }}</span>
                    </div>

                    <ul class="hv-offer-products">
                        @foreach ($order->lines as $line)
                            <li><span>{{ $line->product?->name ?? $line->description }}</span><strong>× {{ (float) $line->quantity }}</strong></li>
                        @endforeach
                    </ul>

                    <div class="hv-offer-meta">
                        <span>
                            <x-filament::icon icon="heroicon-o-check-circle" class="h-4 w-4" />
                            {{ __('huvant-orders::manufacturing.confirmed_on', ['date' => $order->confirmed_at?->format('d/m/Y')]) }}
                        </span>
                        @if ($order->expected_delivery_date)
                            <span>
                                <x-filament::icon icon="heroicon-o-calendar-days" class="h-4 w-4" />
                                {{ __('huvant-orders::manufacturing.due_on', ['date' => $order->expected_delivery_date->format('d/m/Y')]) }}
                            </span>
                        @endif
                        @if ($order->manufacturing_managed_at)
                            <span>
                                <x-filament::icon icon="heroicon-o-user" class="h-4 w-4" />
                                {{ __('huvant-orders::manufacturing.managed_by', ['name' => $order->manufacturingManagedBy?->name ?? '—']) }}
                            </span>
                        @endif
                    </div>

                    <div class="hv-offer-card-state">
                        @if ($activeTab === 'history')
                            <span @class(['hv-state-badge', 'is-complete' => $this->isComplete($order)])>
                                {{ $this->isComplete($order) ? __('huvant-orders::manufacturing.completed') : __('huvant-orders::manufacturing.in_progress') }}
                            </span>
                        @else
                            <span class="hv-offer-open">{{ __('huvant-orders::manufacturing.manage') }}</span>
                        @endif
                        <x-filament::icon icon="heroicon-m-chevron-right" class="h-5 w-5" />
                    </div>
                </a>
            @empty
                <div class="hv-offer-empty">
                    <x-filament::icon icon="{{ $activeTab === 'history' ? 'heroicon-o-clock' : 'heroicon-o-check-circle' }}" class="h-7 w-7" />
                    <span>{{ __('huvant-orders::manufacturing.'.($activeTab === 'history' ? 'no_history' : 'no_to_manage')) }}</span>
                </div>
            @endforelse
        </div>
    </section>
</x-filament-panels::page>
