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
        $isIt = app()->getLocale() === 'it';

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['partner', 'lines']))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label($isIt ? 'Ordine' : 'Order')
                    ->placeholder('—')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('name')
                    ->label($isIt ? 'Offerta' : 'Offer')
                    ->searchable(),
                TextColumn::make('offer_date')
                    ->label($isIt ? 'Data' : 'Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label($isIt ? 'Cliente' : 'Customer')
                    ->placeholder($isIt ? 'Per magazzino' : 'For stock')
                    ->searchable(),
                TextColumn::make('event')
                    ->label($isIt ? 'Evento' : 'Event')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('supply_type')
                    ->label($isIt ? 'Fornitura' : 'Supply')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('state')
                    ->label($isIt ? 'Stato' : 'Status')
                    ->badge(),
                TextColumn::make('production')
                    ->label($isIt ? 'Produzione' : 'Production')
                    ->badge()
                    ->state(fn (Order $record) => $record->state->isVisibleToProduction() ? Orders::productionStatus($record) : null)
                    ->placeholder('—'),
                TextColumn::make('expected_delivery_date')
                    ->label($isIt ? 'Consegna prevista' : 'Expected delivery')
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('total')
                    ->label($isIt ? 'Totale' : 'Total')
                    ->state(fn (Order $record): float => $record->totalAmount())
                    ->money('EUR', locale: 'it')
                    ->visible(Orders::canSeePrices()),
                TextColumn::make('closing_state')
                    ->label($isIt ? 'Chiusura' : 'Closing')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('supply_type')->label($isIt ? 'Fornitura' : 'Supply')->options(SupplyType::class),
                SelectFilter::make('closing_state')->label($isIt ? 'Chiusura' : 'Closing')->options(ClosingState::class),
            ])
            ->recordActions([EditAction::make()]);
    }
}
