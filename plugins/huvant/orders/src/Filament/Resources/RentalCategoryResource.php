<?php

namespace Huvant\Orders\Filament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Huvant\Orders\Filament\Resources\RentalCategoryResource\Pages\ManageRentalCategories;
use Huvant\Orders\Models\RentalCategory;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Model;
use Webkul\Support\Enums\NavigationGroup;

class RentalCategoryResource extends Resource
{
    protected static ?string $model = RentalCategory::class;

    protected static ?string $slug = 'huvant/rental-categories';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Rental category';
    }

    public static function getNavigationLabel(): string
    {
        return 'Rental categories';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Sale;
    }

    /** Everyone sees the categories; only administrators set them up. */
    public static function canCreate(): bool
    {
        return Orders::isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return Orders::isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return Orders::isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Name')->placeholder('Renal biopsy torso')->required()->maxLength(120)->unique(ignoreRecord: true),
            TextInput::make('units')->label('Units available')->numeric()->integer()->minValue(1)->default(1)->required(),
            ColorPicker::make('color')->label('Colour'),
            Textarea::make('notes')->label('Notes')->rows(2)->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')->label(''),
                TextColumn::make('name')->label('Name')->searchable(),
                TextColumn::make('units')->label('Units'),
                TextColumn::make('notes')->label('Notes')->limit(60)->placeholder('—'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageRentalCategories::route('/')];
    }
}
