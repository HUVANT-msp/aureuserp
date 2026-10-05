<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Filament\Resources\OrderResource;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('New offer')];
    }

    public function getTabs(): array
    {
        return [
            'offers' => Tab::make('Offers')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::Draft, OrderState::Sent])),
            'production' => Tab::make('In production')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->whereIn('state', [OrderState::Pending, OrderState::Confirmed])
                    ->where('closing_state', ClosingState::Open)),
            'closed' => Tab::make('Closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->whereIn('state', [OrderState::Rejected, OrderState::Expired, OrderState::Cancelled])
                    ->orWhere('closing_state', '!=', ClosingState::Open))),
            'all' => Tab::make('All'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'production';
    }
}
