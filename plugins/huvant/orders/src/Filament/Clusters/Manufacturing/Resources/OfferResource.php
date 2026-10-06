<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Filament\Clusters\Manufacturing;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource\Pages\ListOffers;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource\Pages\ManageOffer;
use Huvant\Orders\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OfferResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $cluster = Manufacturing::class;

    protected static ?string $slug = 'offers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::manufacturing.offers');
    }

    public static function getModelLabel(): string
    {
        return __('huvant-orders::manufacturing.offer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('huvant-orders::manufacturing.offers');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('state', OrderState::Confirmed);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOffers::route('/'),
            'manage' => ManageOffer::route('/{record}/manage'),
        ];
    }
}
