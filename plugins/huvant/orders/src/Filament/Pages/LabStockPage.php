<?php

namespace Huvant\Orders\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabItemKind;
use Huvant\Orders\Support\LabStock;
use Huvant\Orders\Support\LabUnits;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Shipping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use RuntimeException;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource as InventoryProductResource;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Support\Enums\NavigationGroup;

/** The lab's "Inventario": every raw material with its stock for production and R&D, minimum and expiring lots. */
class LabStockPage extends Page implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected string $view = 'huvant-orders::filament.pages.lab-stock';

    protected static ?string $slug = 'huvant/raw-materials';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }

    public static function getNavigationLabel(): string
    {
        return 'Raw materials';
    }

    public function getTitle(): string
    {
        return 'Raw materials';
    }

    public function getSubheading(): ?string
    {
        return 'Stock for production (consumed by manufacturing orders) and for R&D (portions handed over, use not tracked).';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newMaterial')
                ->label('New raw material')
                ->icon('heroicon-o-plus-circle')
                ->url(InventoryProductResource::getUrl('create', ['role' => ItemRole::Material->value])),
        ];
    }

    protected function warehouse(): Warehouse
    {
        return Shipping::warehouse(current_company_id());
    }

    public function table(Table $table): Table
    {
        $warehouse = $this->warehouse();
        $production = LabStock::location($warehouse, LabStock::PRODUCTION);
        $research = LabStock::location($warehouse, LabStock::RESEARCH);

        return $table
            ->query(Product::query()->with('uom')->where('huvant_role', ItemRole::Material->value))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('reference')->label('Code')->searchable()->placeholder('—'),
                TextColumn::make('name')->label('Item')->searchable()->wrap(),
                TextColumn::make('huvant_cas_number')->label('CAS')->searchable()->placeholder('—')->toggleable(),
                TextColumn::make('huvant_lab_kind')->label('Kind')->formatStateUsing(fn ($state): ?string => LabItemKind::tryFrom((string) $state)?->getLabel())->toggleable(),
                TextColumn::make('production')
                    ->label('Production')
                    ->state(fn (Product $record): string => LabUnits::format($record, LabStock::onHand($record, $production)))
                    ->color(fn (Product $record): ?string => ($minimum = LabStock::minimum($record)) !== null && LabStock::onHand($record, $production) < $minimum ? 'danger' : null),
                TextColumn::make('research')
                    ->label('R&D')
                    ->state(fn (Product $record): string => LabUnits::format($record, LabStock::onHand($record, $research))),
                TextColumn::make('minimum')
                    ->label('Minimum')
                    ->state(fn (Product $record): ?string => ($minimum = LabStock::minimum($record)) === null ? null : LabUnits::format($record, $minimum))
                    ->placeholder('—'),
                TextColumn::make('expiring')
                    ->label('Expiring lots')
                    ->state(fn (Product $record): ?string => LabStock::expiringLots($record)
                        ->map(fn (Lot $lot): string => $lot->name.' '.$lot->expiration_date->format('d/m/y'))
                        ->implode(', ') ?: null)
                    ->color('warning')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('package')->label('Package')->state(fn (Product $record): ?string => LabUnits::describePackage($record))->placeholder('—')->toggleable(),
                TextColumn::make('huvant_supplier')->label('Supplier')->placeholder('—')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('huvant_storage_position')->label('Position')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('huvant_lab_kind')->label('Kind')->options(LabItemKind::class),
                SelectFilter::make('huvant_lab_use')->label('Used for')->options([LabStock::PRODUCTION => 'Production', LabStock::RESEARCH => 'R&D']),
                TernaryFilter::make('below_minimum')
                    ->label('Below minimum')
                    ->queries(
                        true: fn (Builder $query) => $query->whereIn('id', $this->belowMinimumIds()),
                        false: fn (Builder $query) => $query->whereNotIn('id', $this->belowMinimumIds()),
                    ),
            ])
            ->recordActions([
                $this->loadAction(),
                $this->moveToResearchAction(),
                $this->researchLeftAction(),
                ActionGroup::make([
                    $this->discardAction(),
                    $this->minimumAction(),
                ]),
            ]);
    }

    /** @return array<int, int> */
    protected function belowMinimumIds(): array
    {
        $production = LabStock::location($this->warehouse(), LabStock::PRODUCTION);

        return Product::query()->where('huvant_role', ItemRole::Material->value)->get()
            ->filter(fn (Product $product): bool => ($minimum = LabStock::minimum($product)) !== null && LabStock::onHand($product, $production) < $minimum)
            ->pluck('id')
            ->all();
    }

    /** Quantity and the unit it is entered in; the conversion to the item's unit happens on save. @return array<int, mixed> */
    protected function quantityFields(Product $record, bool $withPackage = false, string $label = 'Quantity'): array
    {
        return [
            TextInput::make('quantity')->label($label)->numeric()->minValue(0)->required(),
            Select::make('unit')
                ->label('Unit')
                ->options(LabUnits::options($record, $withPackage))
                ->default($record->uom_id)
                ->required(),
        ];
    }

    protected function mainQuantity(Product $record, array $data): float
    {
        return LabUnits::toMain($record, (float) $data['quantity'], is_numeric($data['unit']) ? (int) $data['unit'] : $data['unit']);
    }

    protected function lotField(Product $record, string $use): ?Select
    {
        if ($record->tracking === ProductTracking::QTY) {
            return null;
        }

        return Select::make('lot_id')
            ->label('Lot')
            ->options(fn (): array => ProductQuantity::query()
                ->with('lot')
                ->where('product_id', $record->id)
                ->where('location_id', LabStock::location($this->warehouse(), $use)->id)
                ->where('quantity', '>', 0)
                ->whereNotNull('lot_id')
                ->get()
                ->mapWithKeys(fn (ProductQuantity $quant): array => [$quant->lot_id => sprintf(
                    '%s · %s%s',
                    $quant->lot->name,
                    LabUnits::format($record, (float) $quant->quantity),
                    $quant->lot->expiration_date ? ' · exp. '.$quant->lot->expiration_date->format('d/m/Y') : '',
                )])
                ->all())
            ->required();
    }

    protected function loadAction(): Action
    {
        return Action::make('load')
            ->label('Load')
            ->icon('heroicon-o-arrow-down-tray')
            ->modalDescription('Goods in, into production stock.')
            ->schema(fn (Product $record): array => array_values(array_filter([
                ...$this->quantityFields($record, withPackage: true),
                $record->tracking !== ProductTracking::QTY ? TextInput::make('lot')->label('Internal lot')->placeholder('L1')->required() : null,
                $record->tracking !== ProductTracking::QTY ? TextInput::make('supplier_lot')->label('Supplier lot number') : null,
                $record->tracking !== ProductTracking::QTY ? DatePicker::make('expires_on')->label('Expiry date') : null,
            ])))
            ->action(function (Product $record, array $data): void {
                $this->run(fn () => LabStock::load(
                    $record,
                    LabStock::location($this->warehouse(), LabStock::PRODUCTION),
                    $this->mainQuantity($record, $data),
                    $data['lot'] ?? null,
                    $data['supplier_lot'] ?? null,
                    filled($data['expires_on'] ?? null) ? Carbon::parse($data['expires_on']) : null,
                ), 'Loaded');
            });
    }

    protected function moveToResearchAction(): Action
    {
        return Action::make('toResearch')
            ->label('To R&D')
            ->icon('heroicon-o-arrow-right-circle')
            ->color('gray')
            ->modalDescription('Hands a portion of production stock over to R&D.')
            ->schema(fn (Product $record): array => array_values(array_filter([
                $this->lotField($record, LabStock::PRODUCTION),
                ...$this->quantityFields($record),
            ])))
            ->action(function (Product $record, array $data): void {
                $this->run(fn () => LabStock::moveToResearch(
                    $record,
                    $this->warehouse(),
                    $this->mainQuantity($record, $data),
                    isset($data['lot_id']) ? Lot::query()->find($data['lot_id']) : null,
                ), 'Moved to R&D');
            });
    }

    protected function researchLeftAction(): Action
    {
        return Action::make('researchLeft')
            ->label('R&D left')
            ->icon('heroicon-o-adjustments-horizontal')
            ->color('gray')
            ->modalDescription('How much of it R&D still has. Nothing else to record: R&D use is not tracked.')
            ->schema(fn (Product $record): array => array_values(array_filter([
                $this->lotField($record, LabStock::RESEARCH),
                Radio::make('finished')
                    ->hiddenLabel()
                    ->options([1 => 'Finished', 0 => 'Some is left'])
                    ->default(1)
                    ->inline()
                    ->live(),
                ...array_map(fn ($field) => $field->visible(fn (Get $get): bool => ! (bool) $get('finished')), $this->quantityFields($record, label: 'Left')),
            ])))
            ->action(function (Product $record, array $data): void {
                $this->run(fn () => LabStock::setResearchRemaining(
                    $record,
                    $this->warehouse(),
                    (bool) $data['finished'] ? 0.0 : $this->mainQuantity($record, $data),
                    isset($data['lot_id']) ? Lot::query()->find($data['lot_id']) : null,
                ), 'R&D stock updated');
            });
    }

    protected function discardAction(): Action
    {
        return Action::make('discard')
            ->label('Discard from production')
            ->icon('heroicon-o-trash')
            ->modalDescription('Expired, spilled or broken: production stock that no manufacturing order used.')
            ->schema(fn (Product $record): array => array_values(array_filter([
                $this->lotField($record, LabStock::PRODUCTION),
                ...$this->quantityFields($record),
            ])))
            ->action(function (Product $record, array $data): void {
                $this->run(fn () => LabStock::unload(
                    $record,
                    LabStock::location($this->warehouse(), LabStock::PRODUCTION),
                    $this->mainQuantity($record, $data),
                    isset($data['lot_id']) ? Lot::query()->find($data['lot_id']) : null,
                ), 'Discarded');
            });
    }

    protected function minimumAction(): Action
    {
        return Action::make('minimum')
            ->label('Minimum stock')
            ->icon('heroicon-o-flag')
            ->visible(fn (): bool => Orders::isAdmin())
            ->schema(fn (Product $record): array => $this->quantityFields($record, withPackage: true, label: 'Keep at least (production)'))
            ->action(fn (Product $record, array $data) => LabStock::setMinimum($record, $this->warehouse(), $this->mainQuantity($record, $data)));
    }

    protected function run(callable $operation, string $success): void
    {
        try {
            $operation();
            Notification::make()->success()->title($success)->send();
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }
}
