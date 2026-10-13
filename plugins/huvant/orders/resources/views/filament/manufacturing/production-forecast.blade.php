<x-filament-panels::page>
    <section class="hv-forecast-summary" aria-label="{{ __('huvant-orders::manufacturing.forecast_summary') }}">
        <div class="hv-forecast-intro">
            <div>
                <h2>{{ __('huvant-orders::manufacturing.production_forecast') }}</h2>
                <p>{{ __('huvant-orders::manufacturing.production_forecast_note') }}</p>
            </div>
            <span class="hv-forecast-status">
                <x-filament::icon icon="heroicon-m-clock" />
                {{ __('huvant-orders::manufacturing.only_pending_offers') }}
            </span>
        </div>

        <dl class="hv-forecast-metrics">
            <div><dt>{{ __('huvant-orders::manufacturing.pending_offers') }}</dt><dd>{{ $offersCount }}</dd></div>
            <div><dt>{{ __('huvant-orders::manufacturing.distinct_products') }}</dt><dd>{{ $productsCount }}</dd></div>
            <div><dt>{{ __('huvant-orders::manufacturing.potential_units') }}</dt><dd>{{ $requestedCount }}</dd></div>
            <div><dt>{{ __('huvant-orders::manufacturing.already_in_lab') }}</dt><dd>{{ $stockCount }}</dd></div>
            <div class="{{ $hasUncovered ? 'is-uncovered' : 'is-covered' }}">
                <dt>{{ __('huvant-orders::manufacturing.total_uncovered') }}</dt><dd>{{ $uncoveredCount }}</dd>
            </div>
        </dl>

        <div class="hv-forecast-note" role="note">
            <x-filament::icon icon="heroicon-o-information-circle" />
            <p>{{ __('huvant-orders::manufacturing.forecast_inventory_note') }}</p>
        </div>
    </section>

    <div class="hv-forecast-table">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
