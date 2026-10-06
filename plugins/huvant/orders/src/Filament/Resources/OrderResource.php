<?php

namespace Huvant\Orders\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\CreateOrder;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\EditOrder;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\ListOrders;
use Huvant\Orders\Filament\Resources\OrderResource\RelationManagers\DeliveriesRelationManager;
use Huvant\Orders\Filament\Resources\OrderResource\RelationManagers\ProductionRelationManager;
use Huvant\Orders\Filament\Resources\OrderResource\Schemas\OrderForm;
use Huvant\Orders\Filament\Resources\OrderResource\Tables\OrdersTable;
use Huvant\Orders\Models\Order;
use Webkul\Support\Enums\NavigationGroup;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'huvant/orders';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'it' ? 'Ordine' : 'Order';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'it' ? 'Offerte e ordini' : 'Offers and orders';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'it' ? 'Offerte e ordini' : 'Offers and orders';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [ProductionRelationManager::class, DeliveriesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit'   => EditOrder::route('/{record}/edit'),
        ];
    }
}
