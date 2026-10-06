<?php

namespace Huvant\Orders\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabItemKind;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Support\Models\UOM;

/** What the order register kept about customers, delivery addresses and items, and the role of each item. */
class RecordFields
{
    public static function register(): void
    {
        PartnerSchemaRegistry::form('general.after', fn (): array => [static::companySection()]);
        PartnerSchemaRegistry::infolist('general.after', fn (): array => [static::companyEntries()]);
        PartnerSchemaRegistry::form('address.append', fn (): array => static::deliveryAddressFields());
        ProductSchemaRegistry::form('left.general.after', fn (): array => [static::roleSection()]);
        ProductSchemaRegistry::form('left.append', fn (): array => [Recipes::section()]);
        ProductSchemaRegistry::form('right.append', fn (): array => [static::productSection(), static::labSection()]);
        ProductSchemaRegistry::infolist('right.append', fn (): array => [static::productEntries()]);
        ProductSchemaRegistry::table('columns', fn (): array => [
            TextColumn::make('huvant_role')->label('Role')->badge()->color('gray')->sortable(),
        ]);
    }

    protected static function isCompany(mixed $accountType): bool
    {
        return in_array($accountType, [AccountType::COMPANY, AccountType::COMPANY->value], true);
    }

    protected static function companySection(): Section
    {
        return Section::make('E-invoicing and customs')
            ->schema([
                TextInput::make('huvant_sdi_code')
                    ->label('SDI recipient code')
                    ->maxLength(7),
                TextInput::make('huvant_pec')
                    ->label('PEC')
                    ->email()
                    ->maxLength(255),
                TextInput::make('huvant_customs_code')
                    ->label('Customs code')
                    ->helperText('For customers outside the EU.')
                    ->maxLength(60),
            ])
            ->columns(3)
            ->visible(fn (Get $get): bool => static::isCompany($get('account_type')));
    }

    protected static function companyEntries(): Section
    {
        return Section::make('E-invoicing and customs')
            ->schema([
                TextEntry::make('huvant_sdi_code')->label('SDI recipient code')->placeholder('—'),
                TextEntry::make('huvant_pec')->label('PEC')->placeholder('—'),
                TextEntry::make('huvant_customs_code')->label('Customs code')->placeholder('—'),
            ])
            ->columns(3)
            ->visible(fn ($record): bool => static::isCompany($record?->account_type));
    }

    /** A delivery address can be an event: its day and who receives the goods there. */
    protected static function deliveryAddressFields(): array
    {
        return [
            DatePicker::make('huvant_event_date')
                ->label('Event date'),
            TextInput::make('huvant_onsite_contact')
                ->label('Contact on site')
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
        return Section::make('Role')
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
        return Section::make('Production and customs')
            ->schema([
                TextInput::make('huvant_production_days')
                    ->label('Production time (days)')
                    ->numeric()
                    ->minValue(0)
                    ->integer()
                    ->visible(fn (Get $get): bool => static::role($get) === ItemRole::Product),
                TextInput::make('huvant_hs_code')
                    ->label('HS code')
                    ->maxLength(20),
            ])
            ->visible(fn (Get $get): bool => in_array(static::role($get), [ItemRole::Product, ItemRole::Rental], true));
    }

    /** Raw materials (reagents, consumables, PPE...) are what the lab stock tracks. */
    protected static function labSection(): Section
    {
        return Section::make('Raw material')
            ->description('Stock is kept in the product unit (g, mL, cm or units): choose it in Pricing.')
            ->schema([
                Select::make('huvant_lab_kind')
                    ->label('Kind')
                    ->options(LabItemKind::class)
                    ->default(LabItemKind::Substance->value)
                    ->required(fn (Get $get): bool => static::role($get) === ItemRole::Material),
                Select::make('huvant_lab_use')
                    ->label('Used for')
                    ->options([LabStock::PRODUCTION => 'Production', LabStock::RESEARCH => 'R&D']),
                TextInput::make('huvant_cas_number')
                    ->label('CAS number')
                    ->maxLength(30),
                TextInput::make('huvant_supplier')
                    ->label('Supplier')
                    ->maxLength(120),
                TextInput::make('huvant_supplier_code')
                    ->label('Supplier product code')
                    ->maxLength(60),
                TextInput::make('huvant_package_quantity')
                    ->label('Package contains')
                    ->helperText('Only a shortcut for loading: "3 packages".')
                    ->numeric()
                    ->minValue(0),
                Select::make('huvant_package_uom_id')
                    ->label('Package unit')
                    ->options(fn (): array => UOM::query()->whereIn('name', ['mg', 'g', 'kg', 'mL', 'L', 'cm', 'm', 'Units'])->pluck('name', 'id')->all()),
                TextInput::make('huvant_density')
                    ->label('Density (g/mL)')
                    ->helperText('Lets quantities be entered in grams or millilitres alike.')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('huvant_storage_position')
                    ->label('Storage position')
                    ->placeholder('Cabinet under the fume hood')
                    ->maxLength(120),
            ])
            ->columns(2)
            ->visible(fn (Get $get): bool => static::role($get) === ItemRole::Material);
    }

    protected static function productEntries(): Section
    {
        return Section::make('Role')
            ->schema([
                TextEntry::make('huvant_role')->label('Role')->badge()->placeholder('—'),
                TextEntry::make('huvant_production_days')->label('Production time (days)')->placeholder('—'),
                TextEntry::make('huvant_hs_code')->label('HS code')->placeholder('—'),
                TextEntry::make('huvant_cas_number')->label('CAS number')->placeholder('—'),
            ]);
    }
}
