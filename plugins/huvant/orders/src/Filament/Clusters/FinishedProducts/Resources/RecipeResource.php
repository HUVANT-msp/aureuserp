<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources;

use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\ShelfLifeUnit;
use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\CreateRecipe;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\EditRecipe;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\ListRecipes;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\ManageRecipeDocuments;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\ManageStock;
use Huvant\Orders\Models\RecipeLine;
use Huvant\Orders\Support\LabInventory;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Models\Product;

/** A product the lab makes (Brain, Kidney Biopsy Pad...): its code prefix, shelf life and recipe. */
class RecipeResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = FinishedProducts::class;

    protected static ?string $slug = 'recipes';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('huvant-orders::lab.product');
    }

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.recipes');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('huvant_role', ItemRole::Product->value);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('huvant-orders::lab.product_name'))
                            ->placeholder('High Grade Brain Pad')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('huvant_code_prefix')
                            ->label(__('huvant-orders::lab.acronym'))
                            ->helperText(fn (Get $get): string => __('huvant-orders::lab.piece_codes', [
                                'code' => (strtoupper((string) $get('huvant_code_prefix')) ?: 'ACR').'-'.today()->format('Ymd').'-01',
                            ]))
                            ->required()
                            ->maxLength(12)
                            ->alphaDash()
                            ->live(onBlur: true)
                            ->dehydrateStateUsing(fn (?string $state): ?string => $state ? strtoupper($state) : null),
                        FusedGroup::make([
                            TextInput::make('huvant_shelf_life')
                                ->numeric()
                                ->integer()
                                ->minValue(1)
                                ->placeholder('12'),
                            Select::make('huvant_shelf_life_unit')
                                ->options(ShelfLifeUnit::class)
                                ->default(ShelfLifeUnit::Months->value),
                        ])
                            ->label(__('huvant-orders::lab.expires_after'))
                            ->columns(2),
                        TextInput::make('price')
                            ->label(__('huvant-orders::lab.list_price'))
                            ->helperText(__('huvant-orders::lab.offered_price_help'))
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (): bool => Orders::canSeePrices()),
                    ])
                    ->columns(2),
                Section::make(__('huvant-orders::lab.recipe'))
                    ->description(__('huvant-orders::lab.recipe_description'))
                    ->schema([
                        Repeater::make('huvant_recipe')
                            ->hiddenLabel()
                            ->dehydrated(false)
                            ->defaultItems(0)
                            ->addActionLabel(__('huvant-orders::lab.add_material'))
                            ->reorderable()
                            ->afterStateHydrated(function (Repeater $component, ?Model $record): void {
                                $component->state($record ? LabInventory::recipe($record)->map(fn (RecipeLine $line): array => [
                                    'material_id' => $line->material_id,
                                    'quantity'    => (float) $line->quantity,
                                ])->all() : []);
                            })
                            ->saveRelationshipsUsing(fn (Model $record, ?array $state) => LabInventory::saveRecipe($record, $state ?? []))
                            ->schema([
                                Select::make('material_id')
                                    ->label(__('huvant-orders::lab.material'))
                                    ->options(fn (): array => LabInventory::materials()->mapWithKeys(fn (Product $material): array => [
                                        $material->id => trim(($material->reference ? "[{$material->reference}] " : '').$material->name),
                                    ])->all())
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                TextInput::make('quantity')
                                    ->label(__('huvant-orders::lab.per_piece'))
                                    ->numeric()
                                    ->minValue(0.0001)
                                    ->required()
                                    ->suffix(fn (Get $get): ?string => Product::query()->find($get('material_id'))?->huvant_package_unit),
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label(__('huvant-orders::lab.product'))->searchable()->sortable(),
                TextColumn::make('huvant_code_prefix')->label(__('huvant-orders::lab.acronym'))->fontFamily('mono')->placeholder('—'),
                TextColumn::make('recipe')
                    ->label(__('huvant-orders::lab.materials'))
                    ->state(fn (Product $record): int => RecipeLine::query()->where('product_id', $record->id)->count()),
                TextColumn::make('shelf_life')
                    ->label(__('huvant-orders::lab.expires_after'))
                    ->state(fn (Product $record): ?string => (int) $record->huvant_shelf_life > 0
                        ? $record->huvant_shelf_life.' '.(ShelfLifeUnit::tryFrom((string) $record->huvant_shelf_life_unit)?->getLabel() ?? '')
                        : null)
                    ->placeholder('—'),
                TextColumn::make('in_lab')
                    ->label(__('huvant-orders::lab.in_the_lab'))
                    ->state(fn (Product $record): int => LabInventory::counts($record)['in_lab']),
            ])
            ->recordUrl(fn (Product $record): string => static::getUrl('edit', ['record' => $record]))
            ->recordActions([EditAction::make()]);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([EditRecipe::class, ManageStock::class, ManageRecipeDocuments::class]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => ListRecipes::route('/'),
            'create'    => CreateRecipe::route('/create'),
            'edit'      => EditRecipe::route('/{record}/edit'),
            'stock'     => ManageStock::route('/{record}/stock'),
            'documents' => ManageRecipeDocuments::route('/{record}/documents'),
        ];
    }
}
