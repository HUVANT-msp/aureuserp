<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Huvant\Orders\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Security\Models\User;

/** The manufacturing orders raised by the order: who makes what, by when, and the hours spent. */
class ProductionRelationManager extends RelationManager
{
    protected static string $relationship = 'manufacturingOrders';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return app()->getLocale() === 'it' ? 'Produzione' : 'Production';
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Order && $ownerRecord->lines()->whereNotNull('manufacturing_order_id')->exists();
    }

    public function table(Table $table): Table
    {
        $isIt = app()->getLocale() === 'it';

        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->with(['product', 'assignedUser']))
            ->columns([
                TextColumn::make('name')->label($isIt ? 'Ordine di produzione' : 'Manufacturing order'),
                TextColumn::make('product.name')->label($isIt ? 'Prodotto' : 'Product'),
                TextColumn::make('quantity')->label($isIt ? 'Q.tà' : 'Qty')->numeric(locale: 'it'),
                TextColumn::make('state')->label($isIt ? 'Stato' : 'Status')->badge(),
                TextColumn::make('deadline_at')->label($isIt ? 'Termine' : 'Deadline')->date('d/m/Y')->placeholder('—'),
                TextColumn::make('assignedUser.name')->label($isIt ? 'Responsabile' : 'Responsible')->placeholder('—'),
                TextColumn::make('huvant_worked_hours')->label($isIt ? 'Ore' : 'Hours')->numeric(locale: 'it')->placeholder('—'),
            ])
            ->recordActions([
                Action::make('work')
                    ->label($isIt ? 'Responsabile e ore lavorate' : 'Responsible and hours')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Select::make('assigned_user_id')->label($isIt ? 'Responsabile' : 'Responsible')->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())->searchable(),
                        TextInput::make('huvant_worked_hours')->label($isIt ? 'Ore lavorate' : 'Hours worked')->numeric()->minValue(0),
                    ])
                    ->fillForm(fn (ManufacturingOrder $record): array => [
                        'assigned_user_id'    => $record->assigned_user_id,
                        'huvant_worked_hours' => $record->huvant_worked_hours,
                    ])
                    // The manufacturing order does not know the hours column: write it directly.
                    ->action(fn (ManufacturingOrder $record, array $data) => ManufacturingOrder::query()->whereKey($record->id)->update([
                        'assigned_user_id'    => $data['assigned_user_id'],
                        'huvant_worked_hours' => $data['huvant_worked_hours'],
                    ])),
                Action::make('open')
                    ->label($isIt ? 'Apri' : 'Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (ManufacturingOrder $record): string => ManufacturingOrderResource::getUrl('view', ['record' => $record])),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
