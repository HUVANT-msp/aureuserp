<?php

namespace Huvant\Orders\Filament\Clusters\RawMaterials\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\LabArea;
use Huvant\Orders\Filament\Clusters\RawMaterials;
use Huvant\Orders\Models\MaterialLot;
use Huvant\Orders\Settings\OrdersSettings;
use Huvant\Orders\Support\LabInventory;
use Huvant\Orders\Support\Orders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use RuntimeException;
use Webkul\Product\Models\Product;

/** The packages of raw materials in the lab, each with its lot, expiry and what is left of it. */
class MaterialsInLab extends Page implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected string $view = 'huvant-orders::filament.lab.table-page';

    protected static ?string $cluster = RawMaterials::class;

    protected static ?string $slug = 'in-lab';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.in_the_lab');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.raw_materials_in_lab');
    }

    protected function getHeaderActions(): array
    {
        return [$this->addAction()];
    }

    public function addAction(): Action
    {
        return Action::make('add')
            ->label(__('huvant-orders::lab.add_to_lab'))
            ->icon('heroicon-o-plus-circle')
            ->modalWidth('lg')
            ->schema([
                Select::make('material_id')
                    ->label(__('huvant-orders::lab.material'))
                    ->options(fn (): array => LabInventory::materials()->mapWithKeys(fn (Product $material): array => [
                        $material->id => trim(($material->reference ? "[{$material->reference}] " : '').$material->name),
                    ])->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $material = Product::query()->find($state);
                        $set('quantity', $material ? (float) $material->huvant_package_quantity : null);
                    }),
                TextInput::make('lot_number')
                    ->label(__('huvant-orders::lab.lot_number'))
                    ->helperText(__('huvant-orders::lab.lot_number_help'))
                    ->required()
                    ->maxLength(80),
                DatePicker::make('expiry_date')
                    ->label(__('huvant-orders::lab.expiry_date')),
                TextInput::make('packages')
                    ->label(__('huvant-orders::lab.packages'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('huvant-orders::lab.each_package_contains'))
                    ->numeric()
                    ->minValue(0.0001)
                    ->required()
                    ->suffix(fn (Get $get): ?string => Product::query()->find($get('material_id'))?->huvant_package_unit),
                Radio::make('area')
                    ->label(__('huvant-orders::lab.for'))
                    ->options(LabArea::class)
                    ->default(LabArea::Production->value)
                    ->inline()
                    ->required(),
                DatePicker::make('received_on')
                    ->label(__('huvant-orders::lab.received_on'))
                    ->default(today()),
                Textarea::make('notes')
                    ->label(__('huvant-orders::lab.notes'))
                    ->rows(2),
            ])
            ->action(function (array $data): void {
                $this->attempt(fn () => LabInventory::addPackages(
                    Product::query()->findOrFail($data['material_id']),
                    $data['lot_number'],
                    filled($data['expiry_date']) ? Carbon::parse($data['expiry_date']) : null,
                    (int) $data['packages'],
                    (float) $data['quantity'],
                    LabArea::from($data['area'] instanceof LabArea ? $data['area']->value : $data['area']),
                    filled($data['received_on']) ? Carbon::parse($data['received_on']) : null,
                    $data['notes'] ?? null,
                ), __('huvant-orders::lab.added_to_lab'));
            });
    }

    public function table(Table $table): Table
    {
        $warningDays = app(OrdersSettings::class)->expiry_warning_days;

        return $table
            ->query(MaterialLot::query()->with('material'))
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('material'))
            ->defaultSort(fn (Builder $query) => $query->orderByRaw('expiry_date is null, expiry_date')->orderBy('id'))
            ->columns([
                TextColumn::make('material.name')
                    ->label(__('huvant-orders::lab.material'))
                    ->description(fn (MaterialLot $record): ?string => $record->material?->reference)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('lot_number')
                    ->label(__('huvant-orders::lab.lot'))
                    ->searchable(),
                TextColumn::make('expiry_date')
                    ->label(__('huvant-orders::lab.expiry'))
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->color(fn (MaterialLot $record): ?string => match (true) {
                        $record->isExpired()                                                          => 'danger',
                        $record->expiry_date?->isBefore(today()->addDays($warningDays)) === true      => 'warning',
                        default                                                                       => null,
                    })
                    ->sortable(),
                ViewColumn::make('remaining')
                    ->label(__('huvant-orders::lab.left_in_package'))
                    ->view('huvant-orders::filament.lab.remaining-bar'),
                TextColumn::make('area')
                    ->label(__('huvant-orders::lab.for'))
                    ->badge()
                    ->color(fn (MaterialLot $record): string => $record->area === LabArea::Research ? 'info' : 'gray'),
                TextColumn::make('received_on')
                    ->label(__('huvant-orders::lab.received'))
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('in_lab')
                    ->label(__('huvant-orders::lab.packages'))
                    ->placeholder(__('huvant-orders::lab.all'))
                    ->trueLabel(__('huvant-orders::lab.in_the_lab'))
                    ->falseLabel(__('huvant-orders::lab.finished'))
                    ->default(true)
                    ->queries(
                        true: fn (Builder $query) => $query->inLab(),
                        false: fn (Builder $query) => $query->where(fn (Builder $query) => $query->whereNotNull('finished_at')->orWhere('remaining_quantity', '<=', 0)),
                    ),
                SelectFilter::make('material_id')
                    ->label(__('huvant-orders::lab.material'))
                    ->options(fn (): array => LabInventory::materials()->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('area')->label(__('huvant-orders::lab.for'))->options(LabArea::class),
                Filter::make('expiring')
                    ->label(__('huvant-orders::lab.expiring_within', ['days' => $warningDays]))
                    ->query(fn (Builder $query) => $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<=', today()->addDays($warningDays))),
            ])
            ->recordActions([
                Action::make('left')
                    ->label(__('huvant-orders::lab.how_much_left'))
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('gray')
                    ->schema(fn (MaterialLot $record): array => [
                        TextInput::make('remaining')
                            ->label(__('huvant-orders::lab.left'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue((float) $record->initial_quantity)
                            ->suffix($record->material?->huvant_package_unit)
                            ->required(),
                    ])
                    ->fillForm(fn (MaterialLot $record): array => ['remaining' => (float) $record->remaining_quantity])
                    ->action(fn (MaterialLot $record, array $data) => $this->attempt(fn () => LabInventory::setRemaining($record, (float) $data['remaining']), __('huvant-orders::lab.updated'))),
                Action::make('finished')
                    ->label(__('huvant-orders::lab.finished'))
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription(__('huvant-orders::lab.package_finished_help'))
                    ->visible(fn (MaterialLot $record): bool => $record->finished_at === null)
                    ->action(fn (MaterialLot $record) => $this->attempt(fn () => LabInventory::finish($record), __('huvant-orders::lab.marked_finished'))),
                ActionGroup::make([
                    Action::make('edit')
                        ->label(__('huvant-orders::lab.edit'))
                        ->icon('heroicon-o-pencil-square')
                        ->schema([
                            TextInput::make('lot_number')->label(__('huvant-orders::lab.lot_number'))->required()->maxLength(80),
                            DatePicker::make('expiry_date')->label(__('huvant-orders::lab.expiry_date')),
                            Radio::make('area')->label(__('huvant-orders::lab.for'))->options(LabArea::class)->inline()->required(),
                            Textarea::make('notes')->label(__('huvant-orders::lab.notes'))->rows(2),
                        ])
                        ->fillForm(fn (MaterialLot $record): array => $record->only(['lot_number', 'expiry_date', 'area', 'notes']))
                        ->action(fn (MaterialLot $record, array $data) => $record->update($data)),
                    DeleteAction::make()->visible(fn (): bool => Orders::isAdmin()),
                ]),
            ])
            ->emptyStateHeading(__('huvant-orders::lab.no_packages'))
            ->emptyStateDescription(__('huvant-orders::lab.no_packages_help'));
    }

    protected function attempt(callable $operation, string $success): void
    {
        try {
            $operation();
            Notification::make()->success()->title($success)->send();
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
    }
}
