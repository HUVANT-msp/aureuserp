<?php

namespace Huvant\Orders\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Huvant\Orders\Settings\OrdersSettings;
use Huvant\Orders\Support\Orders;
use Webkul\Support\Enums\NavigationGroup;

class ManageOrdersSettings extends SettingsPage
{
    protected static string $settings = OrdersSettings::class;

    protected static ?string $slug = 'huvant/orders-settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        return Orders::canSeePrices();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }

    public static function getNavigationLabel(): string
    {
        return 'Orders settings';
    }

    public function getTitle(): string
    {
        return 'Orders settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('hourly_cost')
                ->label('Cost of one hour of lab work (€)')
                ->helperText('Used for the margin of each order, with the hours logged on its manufacturing orders.')
                ->numeric()
                ->minValue(0)
                ->required(),
            TextInput::make('expiry_warning_days')
                ->label('Warn about lots expiring within (days)')
                ->integer()
                ->minValue(1)
                ->required(),
        ]);
    }
}
