<?php

namespace Huvant\Orders\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Huvant\Orders\Enums\ItemRole;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;

/** What the order register kept about customers, delivery addresses and items, and the role of each item. */
class RecordFields
{
    public static function register(): void
    {
        PartnerSchemaRegistry::form('general.after', fn (): array => [static::companySection()]);
        PartnerSchemaRegistry::infolist('general.after', fn (): array => [static::companyEntries()]);
        PartnerSchemaRegistry::form('address.append', fn (): array => static::deliveryAddressFields());
        ProductSchemaRegistry::form('left.general.after', fn (): array => [static::roleSection()]);
        ProductSchemaRegistry::form('right.append', fn (): array => [static::productSection()]);
        ProductSchemaRegistry::infolist('right.append', fn (): array => [static::productEntries()]);
        ProductSchemaRegistry::table('columns', fn (): array => [
            TextColumn::make('huvant_role')->label(app()->getLocale() === 'it' ? 'Ruolo' : 'Role')->badge()->color('gray')->sortable(),
        ]);
    }

    protected static function isCompany(mixed $accountType): bool
    {
        return in_array($accountType, [AccountType::COMPANY, AccountType::COMPANY->value], true);
    }

    protected static function companySection(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Fatturazione elettronica e dogana' : 'E-invoicing and customs')
            ->schema([
                TextInput::make('huvant_sdi_code')
                    ->label($isIt ? 'Codice destinatario SDI' : 'SDI recipient code')
                    ->maxLength(7),
                TextInput::make('huvant_pec')
                    ->label('PEC')
                    ->email()
                    ->maxLength(255),
                TextInput::make('huvant_customs_code')
                    ->label($isIt ? 'Codice doganale' : 'Customs code')
                    ->helperText($isIt ? 'Per clienti al di fuori dell\'UE.' : 'For customers outside the EU.')
                    ->maxLength(60),
            ])
            ->columns(3)
            ->visible(fn (Get $get): bool => static::isCompany($get('account_type')));
    }

    protected static function companyEntries(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Fatturazione elettronica e dogana' : 'E-invoicing and customs')
            ->schema([
                TextEntry::make('huvant_sdi_code')->label($isIt ? 'Codice destinatario SDI' : 'SDI recipient code')->placeholder('—'),
                TextEntry::make('huvant_pec')->label('PEC')->placeholder('—'),
                TextEntry::make('huvant_customs_code')->label($isIt ? 'Codice doganale' : 'Customs code')->placeholder('—'),
            ])
            ->columns(3)
            ->visible(fn ($record): bool => static::isCompany($record?->account_type));
    }

    /** A delivery address can be an event: its day and who receives the goods there. */
    protected static function deliveryAddressFields(): array
    {
        $isIt = app()->getLocale() === 'it';

        return [
            DatePicker::make('huvant_event_date')
                ->label($isIt ? 'Data evento' : 'Event date'),
            TextInput::make('huvant_onsite_contact')
                ->label($isIt ? 'Referente in loco' : 'Contact on site')
                ->maxLength(160),
        ];
    }

    public static function role(Get $get): ?ItemRole
    {
        $role = $get('huvant_role');

        return $role instanceof ItemRole ? $role : ItemRole::tryFrom((string) $role);
    }

    /** Chosen first: it decides what the rest of the page asks for. */
    protected static function roleSection(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Ruolo' : 'Role')
            ->schema([
                Radio::make('huvant_role')
                    ->hiddenLabel()
                    ->options(ItemRole::class)
                    ->default(fn (): ?string => ItemRole::tryFrom((string) request()->query('role'))?->value ?? ItemRole::Product->value)
                    ->required()
                    ->live()
                    ->columns(2),
            ]);
    }

    protected static function productSection(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Produzione e dogana' : 'Production and customs')
            ->schema([
                TextInput::make('huvant_production_days')
                    ->label($isIt ? 'Tempo di produzione (giorni)' : 'Production time (days)')
                    ->numeric()
                    ->minValue(0)
                    ->integer()
                    ->visible(fn (Get $get): bool => static::role($get) === ItemRole::Product),
                TextInput::make('huvant_hs_code')
                    ->label($isIt ? 'Codice doganale HS' : 'HS code')
                    ->maxLength(20),
            ])
            ->visible(fn (Get $get): bool => in_array(static::role($get), [ItemRole::Product, ItemRole::Rental], true));
    }

    protected static function productEntries(): Section
    {
        $isIt = app()->getLocale() === 'it';

        return Section::make($isIt ? 'Ruolo' : 'Role')
            ->schema([
                TextEntry::make('huvant_role')->label($isIt ? 'Ruolo' : 'Role')->badge()->placeholder('—'),
                TextEntry::make('huvant_production_days')->label($isIt ? 'Tempo di produzione (giorni)' : 'Production time (days)')->placeholder('—'),
                TextEntry::make('huvant_hs_code')->label($isIt ? 'Codice HS' : 'HS code')->placeholder('—'),
                TextEntry::make('huvant_cas_number')->label($isIt ? 'Numero CAS' : 'CAS number')->placeholder('—'),
            ]);
    }
}
