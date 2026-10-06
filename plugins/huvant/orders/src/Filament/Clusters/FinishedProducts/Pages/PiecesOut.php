<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Concerns\ShowsUnitMaterials;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Support\LabInventory;

/** Pieces that are not in the lab but still Huvant's (rented, lent), and since when. View only. */
class PiecesOut extends Page implements HasTable
{
    use InteractsWithTable;
    use ShowsUnitMaterials;

    protected string $view = 'huvant-orders::filament.lab.table-page';

    protected static ?string $cluster = FinishedProducts::class;

    protected static ?string $slug = 'out';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.out');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.pieces_out');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductUnit::query()->with('product')->where('status', UnitStatus::Out))
            ->defaultGroup(Group::make('product.name')->label(__('huvant-orders::lab.product'))->titlePrefixedWithLabel(false))
            ->defaultSort('status_since')
            ->columns([
                TextColumn::make('code')->label(__('huvant-orders::lab.code'))->fontFamily('mono')->searchable(),
                TextColumn::make('status_since')->label(__('huvant-orders::lab.out_since'))->date('d/m/Y')->sortable()->placeholder('—'),
                TextColumn::make('days_out')
                    ->label(__('huvant-orders::lab.days'))
                    ->state(fn (ProductUnit $record): ?int => $record->status_since ? (int) $record->status_since->diffInDays(today()) : null)
                    ->placeholder('—'),
                TextColumn::make('expiry_date')->label(__('huvant-orders::lab.expires'))->date('d/m/Y')->placeholder('—'),
                TextColumn::make('notes')->label(__('huvant-orders::lab.notes'))->limit(40)->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('product_id')->label(__('huvant-orders::lab.product'))->options(fn (): array => LabInventory::finishedProducts()->pluck('name', 'id')->all()),
            ])
            ->recordActions([$this->materialsAction()])
            ->emptyStateHeading(__('huvant-orders::lab.nothing_out'))
            ->emptyStateDescription(__('huvant-orders::lab.nothing_out_help'));
    }
}
