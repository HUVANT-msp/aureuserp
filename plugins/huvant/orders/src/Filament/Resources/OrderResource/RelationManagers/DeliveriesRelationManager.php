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
use Illuminate\Support\Carbon;
use RuntimeException;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;

class DeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Shipping';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('huvant_carrier')
                    ->label('Carrier')
                    ->datalist(['DHL', 'UPS', 'FedEx', 'TNT', 'BRT', 'GLS', 'SDA'])
                    ->maxLength(80),
                TextInput::make('huvant_tracking_number')
                    ->label('Waybill / tracking number')
                    ->maxLength(120),
                TextInput::make('huvant_packages')
                    ->label('Packages')
                    ->numeric()
                    ->integer()
                    ->minValue(0),
                TextInput::make('huvant_weight_kg')
                    ->label('Weight (kg)')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('huvant_shipping_cost')
                    ->label('Shipping cost (€)')
                    ->numeric()
                    ->minValue(0)
                    ->visible(Orders::canSeePrices()),
                Select::make('huvant_shipping_status')
                    ->label('Status')
                    ->options(ShippingStatus::class)
                    ->required(),
                DatePicker::make('huvant_delivered_at')
                    ->label('Delivered on'),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Transfer')
                    ->description(fn (Delivery $record): string => $record->isReturn() ? 'Return' : 'Delivery'),
                TextColumn::make('state')
                    ->label('Warehouse')
                    ->badge(),
                TextColumn::make('huvant_delivery_note_number')
                    ->label('DDT')
                    ->placeholder('On validation'),
                TextColumn::make('huvant_shipped_at')
                    ->label('Shipped')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                TextColumn::make('huvant_carrier')
                    ->label('Carrier')
                    ->description(fn (Delivery $record): ?string => $record->huvant_tracking_number)
                    ->placeholder('—'),
                TextColumn::make('huvant_delivered_at')
                    ->label('Delivered')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                TextColumn::make('huvant_shipping_status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('huvant_shipping_cost')
                    ->label('Cost')
                    ->money('EUR', locale: 'it')
                    ->placeholder('—')
                    ->visible(Orders::canSeePrices()),
            ])
            ->recordActions([
                EditAction::make()->label('Shipping details'),
                ActionGroup::make([
                    Action::make('transfer')
                        ->label('Open transfer (lots, validation)')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Delivery $record): string => $record->isReturn()
                            ? ReceiptResource::getUrl('view', ['record' => $record->id])
                            : DeliveryResource::getUrl('view', ['record' => $record->id])),
                    Action::make('deliveryNote')
                        ->label('Delivery note (DDT)')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn (Delivery $record): string => route('huvant.orders.delivery-note', $record->id))
                        ->openUrlInNewTab()
                        ->visible(fn (Delivery $record): bool => filled($record->huvant_delivery_note_number)),
                    $this->shippingAction('delivered', 'Mark delivered today', fn (Delivery $record) => Shipping::markDelivered($record, Carbon::today()))
                        ->visible(fn (Delivery $record): bool => ! $record->isReturn() && $record->state === OperationState::DONE && $record->huvant_shipping_status === ShippingStatus::InTransit),
                    $this->shippingAction('return', 'Register return', fn (Delivery $record) => Shipping::registerReturn($record))
                        ->requiresConfirmation()
                        ->modalDescription('A return transfer brings every unit shipped back to stock, with its lot. Validate it when the goods arrive.')
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
