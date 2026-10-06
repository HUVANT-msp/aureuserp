<?php

namespace Huvant\Orders\Filament\Clusters\RawMaterials\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabItemKind;
use Huvant\Orders\Filament\Clusters\RawMaterials;
use Huvant\Orders\Filament\Clusters\RawMaterials\Resources\MaterialResource\Pages\ManageMaterials;
use Huvant\Orders\Support\LabInventory;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Product\Models\Product;

/** The catalogue of raw materials: what each one is, how it comes, the minimum to keep. */
class MaterialResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = RawMaterials::class;

    protected static ?string $slug = 'catalogue';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    /** Units offered for packages; anything else can be typed. */
    public const UNITS = ['g', 'kg', 'mg', 'mL', 'L', 'pcs', 'cm', 'm', 'sheets'];

    public static function getModelLabel(): string
    {
        return __('huvant-orders::lab.raw_material');
    }

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.catalogue');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('huvant_role', ItemRole::Material->value);
    }

    public static function form(Schema $schema): Schema
    {
        $unit = fn (Get $get): ?string => $get('huvant_package_unit');

        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('huvant-orders::lab.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('reference')
                    ->label(__('huvant-orders::lab.internal_code'))
                    ->placeholder('H-101')
                    ->maxLength(40),
                Select::make('huvant_lab_kind')
                    ->label(__('huvant-orders::lab.kind'))
                    ->options(LabItemKind::class)
                    ->default(LabItemKind::Substance->value)
                    ->required(),
                Fieldset::make(__('huvant-orders::lab.package'))
                    ->schema([
                        TextInput::make('huvant_package_quantity')
                            ->label(__('huvant-orders::lab.contains'))
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        TextInput::make('huvant_package_unit')
                            ->label(__('huvant-orders::lab.unit'))
                            ->datalist(self::UNITS)
                            ->helperText(__('huvant-orders::lab.pick_or_type_unit'))
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(20),
                        TextInput::make('huvant_min_quantity')
                            ->label(__('huvant-orders::lab.minimum_to_keep'))
                            ->helperText(__('huvant-orders::lab.minimum_red_help'))
                            ->numeric()
                            ->minValue(0)
                            ->suffix($unit),
                        TextInput::make('huvant_package_price')
                            ->label(__('huvant-orders::lab.package_price'))
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (): bool => Orders::canSeePrices()),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Fieldset::make(__('huvant-orders::lab.supplier'))
                    ->schema([
                        TextInput::make('huvant_supplier')
                            ->label(__('huvant-orders::lab.company'))
                            ->datalist(fn (): array => Product::query()->whereNotNull('huvant_supplier')->distinct()->orderBy('huvant_supplier')->pluck('huvant_supplier')->all())
                            ->maxLength(120),
                        TextInput::make('huvant_supplier_code')
                            ->label(__('huvant-orders::lab.supplier_product_code'))
                            ->maxLength(60),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                TextInput::make('huvant_cas_number')
                    ->label(__('huvant-orders::lab.cas_number'))
                    ->maxLength(30),
                TextInput::make('huvant_storage_position')
                    ->label(__('huvant-orders::lab.storage_position'))
                    ->placeholder(__('huvant-orders::lab.storage_position_placeholder'))
                    ->maxLength(120),
                Textarea::make('description')
                    ->label(__('huvant-orders::lab.notes'))
                    ->placeholder(__('huvant-orders::lab.notes_placeholder'))
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('reference')->label(__('huvant-orders::lab.code'))->searchable()->sortable()->placeholder('—'),
                TextColumn::make('name')->label(__('huvant-orders::lab.name'))->searchable()->sortable()->wrap(),
                TextColumn::make('huvant_lab_kind')->label(__('huvant-orders::lab.kind'))->formatStateUsing(fn ($state): ?string => LabItemKind::tryFrom((string) $state)?->getLabel())->toggleable(),
                TextColumn::make('package')
                    ->label(__('huvant-orders::lab.package'))
                    ->state(fn (Product $record): ?string => (float) $record->huvant_package_quantity > 0 ? LabInventory::format((float) $record->huvant_package_quantity, $record->huvant_package_unit) : null)
                    ->placeholder('—'),
                TextColumn::make('huvant_min_quantity')
                    ->label(__('huvant-orders::lab.minimum'))
                    ->formatStateUsing(fn ($state, Product $record): string => LabInventory::format((float) $state, $record->huvant_package_unit))
                    ->placeholder('—'),
                TextColumn::make('in_lab')
                    ->label(__('huvant-orders::lab.for_production'))
                    ->state(fn (Product $record): string => LabInventory::format(LabInventory::total($record), $record->huvant_package_unit))
                    ->badge()
                    ->color(fn (Product $record): string => match (LabInventory::level($record, LabInventory::total($record))) {
                        'below' => 'danger',
                        'low'   => 'warning',
                        'ok'    => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('huvant_supplier')->label(__('huvant-orders::lab.supplier'))->searchable()->placeholder('—')->toggleable(),
                TextColumn::make('huvant_package_price')->label(__('huvant-orders::lab.price'))->money('EUR', locale: 'it')->placeholder('—')->visible(fn (): bool => Orders::canSeePrices()),
                TextColumn::make('huvant_cas_number')->label('CAS')->searchable()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('huvant_lab_kind')->label(__('huvant-orders::lab.kind'))->options(LabItemKind::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn (): bool => Orders::isAdmin()),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageMaterials::route('/')];
    }
}
