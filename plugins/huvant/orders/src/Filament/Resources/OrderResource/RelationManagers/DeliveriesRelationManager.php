<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\RelationManagers;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ShippingStatus;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Shipping;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use RuntimeException;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;

class DeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return app()->getLocale() === 'it' ? 'Spedizioni e consegne' : 'Shipping';
    }

    public function form(Schema $schema): Schema
    {
        $isIt = app()->getLocale() === 'it';

        return $schema
            ->components([
                TextInput::make('huvant_carrier')
                    ->label($isIt ? 'Corriere / Vettore' : 'Carrier')
                    ->datalist(['DHL', 'UPS', 'FedEx', 'TNT', 'BRT', 'GLS', 'SDA'])
                    ->maxLength(80),
                TextInput::make('huvant_tracking_number')
                    ->label($isIt ? 'Lettera di vettura / Tracking' : 'Waybill / tracking number')
                    ->maxLength(120),
                TextInput::make('huvant_packages')
                    ->label($isIt ? 'Colli' : 'Packages')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('huvant_weight_kg')
                    ->label($isIt ? 'Peso (kg)' : 'Weight (kg)')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('huvant_shipping_cost')
                    ->label($isIt ? 'Costo di spedizione (€)' : 'Shipping cost (€)')
                    ->numeric()
                    ->minValue(0)
                    ->visible(Orders::canSeePrices()),
                Select::make('huvant_shipping_status')
                    ->label($isIt ? 'Stato spedizione' : 'Status')
                    ->options(ShippingStatus::class)
                    ->required(),
                DatePicker::make('huvant_delivered_at')
                    ->label($isIt ? 'Data di consegna' : 'Delivered on'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        $isIt = app()->getLocale() === 'it';

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label($isIt ? 'Movimento' : 'Transfer')
                    ->description(fn (Delivery $record): string => $record->isReturn() ? ($isIt ? 'Reso' : 'Return') : ($isIt ? 'Spedizione' : 'Delivery')),
                TextColumn::make('state')
                    ->label($isIt ? 'Magazzino' : 'Warehouse')
                    ->badge(),
                TextColumn::make('huvant_delivery_note_number')
                    ->label('DDT')
                    ->placeholder($isIt ? 'Alla convalida' : 'On validation'),
                TextColumn::make('huvant_shipped_at')
                    ->label($isIt ? 'Spedito il' : 'Shipped')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                TextColumn::make('huvant_carrier')
                    ->label($isIt ? 'Vettore' : 'Carrier')
                    ->description(fn (Delivery $record): ?string => $record->huvant_tracking_number)
                    ->placeholder('—'),
                TextColumn::make('huvant_delivered_at')
                    ->label($isIt ? 'Consegnato il' : 'Delivered')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                TextColumn::make('huvant_shipping_status')
                    ->label($isIt ? 'Stato' : 'Status')
                    ->badge(),
                TextColumn::make('huvant_shipping_cost')
                    ->label($isIt ? 'Costo' : 'Cost')
                    ->money('EUR', locale: 'it')
                    ->placeholder('—')
                    ->visible(Orders::canSeePrices()),
            ])
            ->recordActions([
                EditAction::make()->label($isIt ? 'Dettagli spedizione' : 'Shipping details'),
                ActionGroup::make([
                    Action::make('transfer')
                        ->label($isIt ? 'Apri movimento (lotti, convalida)' : 'Open transfer (lots, validation)')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Delivery $record): string => $record->isReturn()
                            ? ReceiptResource::getUrl('view', ['record' => $record->id])
                            : DeliveryResource::getUrl('view', ['record' => $record->id])),
                    Action::make('deliveryNote')
                        ->label($isIt ? 'Documento di trasporto (DDT)' : 'Delivery note (DDT)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn (Delivery $record): string => route('huvant.orders.delivery-note', $record->id))
                        ->openUrlInNewTab()
                        ->visible(fn (Delivery $record): bool => filled($record->huvant_delivery_note_number)),
                    $this->shippingAction('delivered', $isIt ? 'Segna come consegnato oggi' : 'Mark delivered today', fn (Delivery $record) => Shipping::markDelivered($record, Carbon::today()))
                        ->visible(fn (Delivery $record): bool => ! $record->isReturn() && $record->state === OperationState::DONE && $record->huvant_shipping_status === ShippingStatus::InTransit),
                    $this->shippingAction('return', $isIt ? 'Registra reso' : 'Register return', fn (Delivery $record) => Shipping::registerReturn($record))
                        ->requiresConfirmation()
                        ->modalDescription($isIt ? "Il movimento di reso ripristina a magazzino le unità spedite con i relativi lotti. Convalidalo all'arrivo della merce." : 'A return transfer brings every unit shipped back to stock, with its lot. Validate it when the goods arrive.')
                        ->visible(fn (Delivery $record): bool => ! $record->isReturn() && $record->state === OperationState::DONE && $this->getOwnerRecord()->supply_type->isReturnable()),
                ]),
            ]);
    }

    protected function shippingAction(string $name, string $label, Closure $apply): Action
    {
        return Action::make($name)
            ->label($label)
            ->action(function (Delivery $record) use ($apply): void {
                try {
                    $apply($record);
                } catch (RuntimeException $e) {
                    Notification::make()->danger()->title($e->getMessage())->send();
                }
            });
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
