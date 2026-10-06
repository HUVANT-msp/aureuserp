<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Concerns\ShowsUnitMaterials;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Support\LabInventory;

/** Pieces sold, with the material lots each was made from. View only. */
class PiecesSold extends Page implements HasTable
{
    use InteractsWithTable;
    use ShowsUnitMaterials;

    protected string $view = 'huvant-orders::filament.lab.table-page';

    protected static ?string $cluster = FinishedProducts::class;

    protected static ?string $slug = 'sold';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.sold');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.pieces_sold');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductUnit::query()->with('product')->withCount('materials')->where('status', UnitStatus::Sold))
            ->defaultSort('status_since', 'desc')
            ->columns([
                TextColumn::make('code')->label(__('huvant-orders::lab.code'))->fontFamily('mono')->searchable(),
                TextColumn::make('product.name')->label(__('huvant-orders::lab.product'))->searchable(),
                TextColumn::make('production_date')->label(__('huvant-orders::lab.produced'))->date('d/m/Y')->sortable(),
                TextColumn::make('status_since')->label(__('huvant-orders::lab.sold_on'))->date('d/m/Y')->sortable()->placeholder('—'),
                TextColumn::make('materials_count')->label(__('huvant-orders::lab.material_lots')),
                TextColumn::make('notes')->label(__('huvant-orders::lab.notes'))->limit(40)->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('product_id')->label(__('huvant-orders::lab.product'))->options(fn (): array => LabInventory::finishedProducts()->pluck('name', 'id')->all()),
            ])
            ->recordActions([$this->materialsAction()])
            ->emptyStateHeading(__('huvant-orders::lab.nothing_sold'))
            ->emptyStateDescription(__('huvant-orders::lab.nothing_sold_help'));
    }
}
