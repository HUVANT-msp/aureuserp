<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Huvant\Orders\Enums\Fulfilment;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Enums\AddressType;
use Webkul\Product\Models\Product;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn as RepeaterTableColumn;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([static::customerSection(), static::linesSection()])
                    ->columnSpan(['lg' => 2]),
                Group::make()
                    ->schema([static::supplySection(), static::datesSection(), static::totalsSection()])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    /** Lines, customer and supply are frozen once the offer is confirmed or closed. */
    protected static function isLocked(?Order $record): bool
    {
        return $record !== null && ! $record->state->isOffer();
    }

    protected static function customerSection(): Section
    {
        return Section::make('Customer')
            ->schema([
                Select::make('partner_id')
                    ->label('Company')
                    ->relationship('partner', 'name', fn (Builder $query) => $query->where('account_type', AccountType::COMPANY->value))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(fn (Get $get): bool => static::fulfilment($get)->needsCustomer())
                    ->afterStateUpdated(function (Set $set): void {
                        $set('contact_id', null);
                        $set('delivery_address_id', null);
                    })
                    ->columnSpan(2),
                Select::make('contact_id')
                    ->label('Contact (Attn)')
                    ->relationship('contact', 'name', fn (Builder $query, Get $get) => $query
                        ->where('account_type', AccountType::INDIVIDUAL->value)
                        ->where('parent_id', $get('partner_id')))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => blank($get('partner_id'))),
                Select::make('delivery_address_id')
                    ->label('Delivery address')
                    ->helperText('Empty: the registered office.')
                    ->relationship('deliveryAddress', 'name', fn (Builder $query, Get $get) => $query
                        ->where('account_type', AccountType::ADDRESS->value)
                        ->where('sub_type', AddressType::DELIVERY->value)
                        ->where('parent_id', $get('partner_id')))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => blank($get('partner_id'))),
                TextInput::make('event')
                    ->label('Event')
                    ->maxLength(160),
                TextInput::make('subject')
                    ->label('Subject')
                    ->placeholder('Supply of haptic simulators')
                    ->maxLength(255),
            ])
            ->columns(2)
            ->disabled(fn (?Order $record): bool => static::isLocked($record));
    }

    protected static function linesSection(): Section
    {
        $canSeePrices = Orders::canSeePrices();

        return Section::make('Products')
            ->schema([
                Repeater::make('lines')
                    ->relationship('lines')
                    ->hiddenLabel()
                    ->defaultItems(0)
                    ->compact()
                    ->orderColumn('sort')
                    ->addActionLabel('Add product')
                    ->table(array_values(array_filter([
                        RepeaterTableColumn::make('product_id')->label('Product')->markAsRequired(),
                        RepeaterTableColumn::make('description')->label('Description'),
                        RepeaterTableColumn::make('quantity')->label('Quantity')->markAsRequired(),
                        $canSeePrices ? RepeaterTableColumn::make('unit_price')->label('Unit price') : null,
                        $canSeePrices ? RepeaterTableColumn::make('discount')->label('Discount %') : null,
                    ])))
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Product $record): string => $record->reference ? "[{$record->reference}] {$record->name}" : $record->name)
                            ->searchable(['name', 'reference'])
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                $product = Product::query()->find($state);
                                // The list price is proposed; it can be changed on the line.
                                $set('unit_price', $product?->price ?? 0);
                            }),
                        TextInput::make('description')
                            ->maxLength(255),
                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(0.0001)
                            ->default(1)
                            ->required(),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->visible($canSeePrices),
                        TextInput::make('discount')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->visible($canSeePrices),
                    ]),
            ])
            ->disabled(fn (?Order $record): bool => static::isLocked($record));
    }

    protected static function supplySection(): Section
    {
        return Section::make('Supply')
            ->schema([
                Select::make('supply_type')
                    ->label('Supply type')
                    ->options(SupplyType::class)
                    ->default(SupplyType::Sale->value)
                    ->required()
                    ->live(),
                Select::make('fulfilment')
                    ->label('Fulfilment')
                    ->options(Fulfilment::class)
                    ->default(Fulfilment::Courier->value)
                    ->required()
                    ->live(),
                Select::make('hand_delivery_user_id')
                    ->label('Delivered by')
                    ->relationship('handDeliveryUser', 'name')
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get): bool => static::fulfilment($get) === Fulfilment::HandDelivery)
                    ->visible(fn (Get $get): bool => static::fulfilment($get) === Fulfilment::HandDelivery),
                TextInput::make('payment_terms')
                    ->label('Payment terms')
                    ->placeholder('Bank transfer 15 days after invoice')
                    ->maxLength(255)
                    ->visible(Orders::canSeePrices()),
            ])
            ->disabled(fn (?Order $record): bool => static::isLocked($record));
    }

    protected static function datesSection(): Section
    {
        return Section::make('Dates')
            ->schema([
                DatePicker::make('offer_date')
                    ->label('Offer date')
                    ->default(today())
                    ->required()
                    ->disabled(fn (?Order $record): bool => $record !== null),
                DatePicker::make('validity_date')
                    ->label('Valid until')
                    ->default(today()->addDays(15)),
                DatePicker::make('expected_delivery_date')
                    ->label('Expected delivery'),
                DatePicker::make('rental_starts_on')
                    ->label('Rental from')
                    ->visible(fn (Get $get): bool => static::rents($get)),
                DatePicker::make('rental_ends_on')
                    ->label('Rental until')
                    ->afterOrEqual('rental_starts_on')
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('expected_return_date', $state))
                    ->visible(fn (Get $get): bool => static::rents($get)),
                DatePicker::make('expected_return_date')
                    ->label('Expected back by')
                    ->visible(fn (Get $get): bool => static::supplyType($get)->isReturnable()),
                Placeholder::make('confirmed_at')
                    ->label('Confirmed on')
                    ->content(fn (?Order $record): string => $record?->confirmed_at?->format('d/m/Y H:i') ?? '—')
                    ->visible(fn (?Order $record): bool => $record?->confirmed_at !== null),
                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(3),
            ]);
    }

    protected static function totalsSection(): Section
    {
        return Section::make('Totals')
            ->schema([
                TextInput::make('vat_rate')
                    ->label('VAT %')
                    ->helperText('0 when VIES confirms the customer is exempt.')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(22)
                    ->required()
                    ->disabled(fn (?Order $record): bool => static::isLocked($record)),
                Placeholder::make('totals')
                    ->hiddenLabel()
                    ->content(fn (?Order $record): string => $record
                        ? sprintf(
                            'Subtotal %s · VAT %s · Total %s',
                            Number::currency($record->untaxedAmount(), 'EUR', 'it'),
                            Number::currency($record->taxAmount(), 'EUR', 'it'),
                            Number::currency($record->totalAmount(), 'EUR', 'it'),
                        )
                        : 'Saved with the offer.'),
                Placeholder::make('margin')
                    ->label('Margin')
                    ->content(function (?Order $record): string {
                        $costing = Orders::costing($record);
                        $euro = fn (float $amount): string => Number::currency($amount, 'EUR', 'it');

                        return sprintf(
                            '%s%s · cost %s (materials %s, lab %s, other %s, shipping %s)',
                            $euro($costing['margin']),
                            $costing['margin_percent'] === null ? '' : " ({$costing['margin_percent']}%)",
                            $euro($costing['cost']),
                            $euro($costing['materials']),
                            $euro($costing['labour']),
                            $euro($costing['other']),
                            $euro($costing['shipping']),
                        );
                    })
                    ->visible(fn (?Order $record): bool => $record !== null),
            ])
            ->visible(Orders::canSeePrices());
    }

    /** A loan, or any line renting something from a rental category. */
    protected static function rents(Get $get): bool
    {
        if (static::supplyType($get) === SupplyType::Loan) {
            return true;
        }

        $productIds = collect($get('lines') ?? [])->pluck('product_id')->filter()->all();

        return $productIds !== [] && Product::query()->whereIn('id', $productIds)->whereNotNull('huvant_rental_category_id')->exists();
    }

    protected static function supplyType(Get $get): SupplyType
    {
        $value = $get('supply_type');

        return $value instanceof SupplyType ? $value : (SupplyType::tryFrom((string) $value) ?? SupplyType::Sale);
    }

    protected static function fulfilment(Get $get): Fulfilment
    {
        $value = $get('fulfilment');

        return $value instanceof Fulfilment ? $value : (Fulfilment::tryFrom((string) $value) ?? Fulfilment::Courier);
    }
}
