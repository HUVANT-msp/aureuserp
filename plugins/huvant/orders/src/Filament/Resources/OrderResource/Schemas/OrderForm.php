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
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\ItemRoles;
use Huvant\Orders\Support\ManufacturingFlow;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
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
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Cliente' : 'Customer')
            ->schema([
                Select::make('partner_id')
                    ->label($isIt ? 'Azienda' : 'Company')
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
                    ->label($isIt ? 'Contatto (C.a.)' : 'Contact (Attn)')
                    ->relationship('contact', 'name', fn (Builder $query, Get $get) => $query
                        ->where('account_type', AccountType::INDIVIDUAL->value)
                        ->where('parent_id', $get('partner_id')))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => blank($get('partner_id'))),
                Select::make('delivery_address_id')
                    ->label($isIt ? 'Indirizzo di consegna' : 'Delivery address')
                    ->helperText($isIt ? 'Vuoto: sede legale.' : 'Empty: the registered office.')
                    ->relationship('deliveryAddress', 'name', fn (Builder $query, Get $get) => $query
                        ->where('account_type', AccountType::ADDRESS->value)
                        ->where('sub_type', AddressType::DELIVERY->value)
                        ->where('parent_id', $get('partner_id')))
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => blank($get('partner_id'))),
                TextInput::make('event')
                    ->label($isIt ? 'Evento' : 'Event')
                    ->maxLength(160),
                TextInput::make('subject')
                    ->label($isIt ? 'Oggetto' : 'Subject')
                    ->placeholder($isIt ? 'Fornitura di simulatori aptici' : 'Supply of haptic simulators')
                    ->maxLength(255),
            ])
            ->columns(2)
            ->disabled(fn (?Order $record): bool => static::isLocked($record));
    }

    protected static function linesSection(): Section
    {
        $canSeePrices = Orders::canSeePrices();
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Articoli / Prodotti' : 'Products')
            ->schema([
                Repeater::make('lines')
                    ->relationship('lines')
                    ->hiddenLabel()
                    ->defaultItems(0)
                    ->compact()
                    ->orderColumn('sort')
                    ->addActionLabel($isIt ? 'Aggiungi articolo' : 'Add product')
                    ->table(array_values(array_filter([
                        RepeaterTableColumn::make('product_id')->label($isIt ? 'Articolo' : 'Product')->markAsRequired(),
                        RepeaterTableColumn::make('description')->label($isIt ? 'Descrizione' : 'Description'),
                        RepeaterTableColumn::make('quantity')->label($isIt ? 'Quantità' : 'Quantity')->markAsRequired(),
                        RepeaterTableColumn::make('stock_availability')->label($isIt ? 'Giacenza' : 'Stock'),
                        $canSeePrices ? RepeaterTableColumn::make('unit_price')->label($isIt ? 'Prezzo unitario' : 'Unit price') : null,
                        $canSeePrices ? RepeaterTableColumn::make('discount')->label($isIt ? 'Sconto %' : 'Discount %') : null,
                    ])))
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name', fn (Builder $query) => ItemRoles::scope($query, ItemRole::sellable()))
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
                            ->required()
                            ->live(debounce: 300),
                        Placeholder::make('stock_availability')
                            ->hiddenLabel()
                            ->content(fn (Get $get): HtmlString|string => static::stockAvailability($get, $isIt)),
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
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Fornitura' : 'Supply')
            ->schema([
                Select::make('supply_type')
                    ->label($isIt ? 'Tipo fornitura' : 'Supply type')
                    ->options(SupplyType::class)
                    ->default(SupplyType::Sale->value)
                    ->required()
                    ->live(),
                Select::make('fulfilment')
                    ->label($isIt ? 'Modalità di consegna' : 'Fulfilment')
                    ->options(Fulfilment::class)
                    ->default(Fulfilment::Courier->value)
                    ->required()
                    ->live(),
                Select::make('hand_delivery_user_id')
                    ->label($isIt ? 'Consegnato da' : 'Delivered by')
                    ->relationship('handDeliveryUser', 'name')
                    ->searchable()
                    ->preload()
                    ->required(fn (Get $get): bool => static::fulfilment($get) === Fulfilment::HandDelivery)
                    ->visible(fn (Get $get): bool => static::fulfilment($get) === Fulfilment::HandDelivery),
                TextInput::make('payment_terms')
                    ->label($isIt ? 'Condizioni di pagamento' : 'Payment terms')
                    ->placeholder($isIt ? 'Bonifico bancario 15 giorni data fattura' : 'Bank transfer 15 days after invoice')
                    ->maxLength(255)
                    ->visible(Orders::canSeePrices()),
            ])
            ->disabled(fn (?Order $record): bool => static::isLocked($record));
    }

    protected static function datesSection(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Date e scadenze' : 'Dates')
            ->schema([
                DatePicker::make('offer_date')
                    ->label($isIt ? 'Data offerta' : 'Offer date')
                    ->default(today())
                    ->required()
                    ->disabled(fn (?Order $record): bool => $record !== null),
                DatePicker::make('validity_date')
                    ->label($isIt ? 'Valida fino al' : 'Valid until')
                    ->default(today()->addDays(15)),
                DatePicker::make('expected_delivery_date')
                    ->label($isIt ? 'Consegna prevista' : 'Expected delivery'),
                DatePicker::make('rental_starts_on')
                    ->label($isIt ? 'Noleggio dal' : 'Rental from')
                    ->visible(fn (Get $get): bool => static::rents($get)),
                DatePicker::make('rental_ends_on')
                    ->label($isIt ? 'Noleggio al' : 'Rental until')
                    ->afterOrEqual('rental_starts_on')
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('expected_return_date', $state))
                    ->visible(fn (Get $get): bool => static::rents($get)),
                DatePicker::make('expected_return_date')
                    ->label($isIt ? 'Rientro previsto entro' : 'Expected back by')
                    ->visible(fn (Get $get): bool => static::supplyType($get)->isReturnable()),
                Placeholder::make('confirmed_at')
                    ->label($isIt ? 'Confermato il' : 'Confirmed on')
                    ->content(fn (?Order $record): string => $record?->confirmed_at?->format('d/m/Y H:i') ?? '—')
                    ->visible(fn (?Order $record): bool => $record?->confirmed_at !== null),
                Textarea::make('notes')
                    ->label($isIt ? 'Note' : 'Notes')
                    ->rows(3),
            ]);
    }

    protected static function totalsSection(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Importi e margini' : 'Totals')
            ->schema([
                TextInput::make('vat_rate')
                    ->label($isIt ? 'IVA %' : 'VAT %')
                    ->helperText($isIt ? '0 se il cliente è esente da IVA (es. verifica VIES).' : '0 when VIES confirms the customer is exempt.')
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
                            $isIt ? 'Subtotale %s · IVA %s · Totale %s' : 'Subtotal %s · VAT %s · Total %s',
                            Number::currency($record->untaxedAmount(), 'EUR', 'it'),
                            Number::currency($record->taxAmount(), 'EUR', 'it'),
                            Number::currency($record->totalAmount(), 'EUR', 'it'),
                        )
                        : ($isIt ? "Salvato con l'offerta." : 'Saved with the offer.')),
                Placeholder::make('margin')
                    ->label($isIt ? 'Margine' : 'Margin')
                    ->content(function (?Order $record) use ($isIt): string {
                        $costing = Orders::costing($record);
                        $euro = fn (float $amount): string => Number::currency($amount, 'EUR', 'it');

                        return sprintf(
                            $isIt ? '%s%s · costo %s (materiali %s, lavorazione %s, altro %s, spedizione %s)' : '%s%s · cost %s (materials %s, lab %s, other %s, shipping %s)',
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

    /** A loan, or any line renting an item. */
    protected static function rents(Get $get): bool
    {
        if (static::supplyType($get) === SupplyType::Loan) {
            return true;
        }

        $productIds = collect($get('lines') ?? [])->pluck('product_id')->filter()->all();

        return $productIds !== [] && Product::query()->whereIn('id', $productIds)->where('huvant_role', ItemRole::Rental->value)->exists();
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

    protected static function stockAvailability(Get $get, bool $isIt): HtmlString|string
    {
        $product = Product::query()->find($get('product_id'));

        if (! $product || $product->huvant_role !== ItemRole::Product) {
            return '—';
        }

        $requested = max(0, (int) ceil((float) ($get('quantity') ?: 0)));
        $available = ManufacturingFlow::stockAvailable($product);
        $usable = min($requested, $available);
        $enough = $requested > 0 && $available >= $requested;
        $text = $isIt
            ? "{$usable} di {$requested} disponibili in giacenza"
            : "{$usable} of {$requested} available in stock";

        return new HtmlString(sprintf(
            '<span class="hv-stock-availability %s" title="%s" aria-label="%s"><span class="hv-stock-dot"></span><span>%d/%d</span></span>',
            $enough ? 'is-available' : 'is-short',
            e($text),
            e($text),
            $usable,
            $requested,
        ));
    }
}
