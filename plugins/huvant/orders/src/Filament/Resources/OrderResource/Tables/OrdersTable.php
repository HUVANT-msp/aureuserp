<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['partner', 'lines']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order')
                    ->placeholder('—')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label('Offer')
                    ->searchable(),
                TextColumn::make('offer_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label('Customer')
                    ->placeholder('For stock')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Event')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('supply_type')
                    ->label('Supply')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('state')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('production')
                    ->label('Production')
                    ->badge()
                    ->state(fn (Order $record) => $record->state->isVisibleToProduction() ? Orders::productionStatus($record) : null)
                    ->placeholder('—'),
                TextColumn::make('expected_delivery_date')
                    ->label('Expected delivery')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (Order $record): float => $record->totalAmount())
                    ->money('EUR', locale: 'it')
                    ->visible(Orders::canSeePrices()),
                TextColumn::make('closing_state')
                    ->label('Closing')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supply_type')->label('Supply')->options(SupplyType::class),
                SelectFilter::make('closing_state')->label('Closing')->options(ClosingState::class),
            ])
            ->recordActions([EditAction::make()]);
    }
}
