<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\Manufacturing;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Models\ProductUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;

class ProductionForecast extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'huvant-orders::filament.manufacturing.production-forecast';

    protected static ?string $cluster = Manufacturing::class;

    protected static ?string $slug = 'production-forecast';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?int $navigationSort = 2;

    /** @var Collection<int, float>|null */
    private ?Collection $pendingDemand = null;

    /** @var Collection<int, int>|null */
    private ?Collection $labStock = null;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::manufacturing.production_forecast_short');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::manufacturing.production_forecast');
    }

    public function getSubheading(): ?string
    {
        return __('huvant-orders::manufacturing.production_forecast_help');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::forecastQuery()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(static::forecastQuery()->with(['order.partner', 'product']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('order.offer_date')
                    ->label(__('huvant-orders::manufacturing.offer_date'))
                    ->extraCellAttributes([
                        'data-column' => 'offer-date',
                        'data-label'  => __('huvant-orders::manufacturing.offer_date'),
                    ])
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('order.name')
                    ->label(__('huvant-orders::manufacturing.offer'))
                    ->extraCellAttributes([
                        'data-column' => 'offer',
                        'data-label'  => __('huvant-orders::manufacturing.offer'),
                    ])
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('huvant-orders::manufacturing.offer_copied')),
                TextColumn::make('order.partner.name')
                    ->label(__('huvant-orders::manufacturing.customer'))
                    ->extraCellAttributes([
                        'data-column' => 'customer',
                        'data-label'  => __('huvant-orders::manufacturing.customer'),
                    ])
                    ->description(fn (OrderLine $record): ?string => $record->order?->event ?: $record->order?->subject)
                    ->placeholder(__('huvant-orders::manufacturing.stock_order'))
                    ->searchable(),
                TextColumn::make('order.expected_delivery_date')
                    ->label(__('huvant-orders::manufacturing.expected_delivery'))
                    ->extraCellAttributes([
                        'data-column' => 'delivery',
                        'data-label'  => __('huvant-orders::manufacturing.expected_delivery'),
                    ])
                    ->date('d/m/Y')
                    ->placeholder(__('huvant-orders::manufacturing.to_define'))
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('huvant-orders::manufacturing.product'))
                    ->extraCellAttributes([
                        'data-column' => 'product',
                        'data-label'  => __('huvant-orders::manufacturing.product'),
                    ])
                    ->description(fn (OrderLine $record): ?string => $record->product?->reference)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('huvant-orders::manufacturing.offer_quantity'))
                    ->extraCellAttributes([
                        'data-column' => 'quantity',
                        'data-label'  => __('huvant-orders::manufacturing.offer_quantity'),
                    ])
                    ->state(fn (OrderLine $record): string => $this->formatQuantity((float) $record->quantity))
                    ->alignEnd(),
                TextColumn::make('pending_demand')
                    ->label(__('huvant-orders::manufacturing.pending_demand'))
                    ->extraCellAttributes([
                        'data-column' => 'pending-demand',
                        'data-label'  => __('huvant-orders::manufacturing.pending_demand'),
                    ])
                    ->state(fn (OrderLine $record): string => $this->formatQuantity($this->pendingDemandFor($record->product_id)))
                    ->tooltip(__('huvant-orders::manufacturing.pending_demand_help'))
                    ->alignEnd(),
                TextColumn::make('lab_stock')
                    ->label(__('huvant-orders::manufacturing.lab_stock'))
                    ->extraCellAttributes([
                        'data-column' => 'lab-stock',
                        'data-label'  => __('huvant-orders::manufacturing.lab_stock'),
                    ])
                    ->state(fn (OrderLine $record): int => $this->labStockFor($record->product_id))
                    ->tooltip(__('huvant-orders::manufacturing.lab_stock_help'))
                    ->alignEnd(),
                TextColumn::make('uncovered')
                    ->label(__('huvant-orders::manufacturing.uncovered'))
                    ->extraCellAttributes([
                        'data-column' => 'uncovered',
                        'data-label'  => __('huvant-orders::manufacturing.uncovered'),
                    ])
                    ->state(function (OrderLine $record): string {
                        $uncovered = $this->uncoveredFor($record->product_id);

                        return $uncovered > 0
                            ? __('huvant-orders::manufacturing.to_produce_count', ['count' => $this->formatQuantity($uncovered)])
                            : __('huvant-orders::manufacturing.covered');
                    })
                    ->badge()
                    ->color(fn (OrderLine $record): string => match (true) {
                        $this->uncoveredFor($record->product_id) <= 0 => 'success',
                        $this->labStockFor($record->product_id) > 0   => 'warning',
                        default                                       => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label(__('huvant-orders::manufacturing.product'))
                    ->options(fn (): array => Product::query()
                        ->where('huvant_role', ItemRole::Product->value)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload(),
                SelectFilter::make('customer')
                    ->label(__('huvant-orders::manufacturing.customer'))
                    ->options(fn (): array => Partner::query()
                        ->whereIn('id', Order::query()
                            ->select('partner_id')
                            ->whereNotNull('partner_id')
                            ->whereIn('state', static::forecastStates()))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'order',
                            fn (Builder $query): Builder => $query->where('partner_id', $data['value']),
                        ),
                    ))
                    ->searchable()
                    ->preload(),
            ])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading(__('huvant-orders::manufacturing.no_unapproved_forecast'))
            ->emptyStateDescription(__('huvant-orders::manufacturing.no_unapproved_forecast_help'));
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $lines = static::forecastQuery()->get(['id', 'order_id', 'product_id', 'quantity']);
        $productIds = $lines->pluck('product_id')->unique();

        return [
            'offersCount'    => $lines->pluck('order_id')->unique()->count(),
            'productsCount'  => $productIds->count(),
            'requestedCount' => $this->formatQuantity((float) $lines->sum('quantity')),
            'stockCount'     => $productIds->sum(fn (int $productId): int => $this->labStockFor($productId)),
            'uncoveredCount' => $this->formatQuantity((float) $productIds->sum(fn (int $productId): float => $this->uncoveredFor($productId))),
            'hasUncovered'   => $productIds->contains(fn (int $productId): bool => $this->uncoveredFor($productId) > 0),
        ];
    }

    protected static function forecastQuery(): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn (Builder $query): Builder => $query->whereIn('state', static::forecastStates()))
            ->whereHas('product', fn (Builder $query): Builder => $query->where('huvant_role', ItemRole::Product->value));
    }

    /** @return list<string> */
    protected static function forecastStates(): array
    {
        return [
            OrderState::Draft->value,
            OrderState::Sent->value,
            OrderState::Pending->value,
        ];
    }

    protected function pendingDemandFor(int $productId): float
    {
        $this->pendingDemand ??= static::forecastQuery()
            ->selectRaw('product_id, SUM(quantity) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id')
            ->map(fn (mixed $quantity): float => (float) $quantity);

        return (float) ($this->pendingDemand[$productId] ?? 0);
    }

    protected function labStockFor(int $productId): int
    {
        $this->labStock ??= ProductUnit::query()
            ->where('status', UnitStatus::InLab->value)
            ->selectRaw('product_id, COUNT(*) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id')
            ->map(fn (mixed $quantity): int => (int) $quantity);

        return (int) ($this->labStock[$productId] ?? 0);
    }

    protected function uncoveredFor(int $productId): float
    {
        return max(0, $this->pendingDemandFor($productId) - $this->labStockFor($productId));
    }

    protected function formatQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',');
    }
}
