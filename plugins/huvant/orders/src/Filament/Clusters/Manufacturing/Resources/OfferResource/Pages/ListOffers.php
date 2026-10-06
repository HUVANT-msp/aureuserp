<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource\Pages;

use Filament\Resources\Pages\Page;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\ManufacturingFlow;

class ListOffers extends Page
{
    protected static string $resource = OfferResource::class;

    protected string $view = 'huvant-orders::filament.manufacturing.offers';

    public string $activeTab = 'to_manage';

    public function getTitle(): string
    {
        return __('huvant-orders::manufacturing.offers');
    }

    /** @return array<int, mixed> */
    public function getSubNavigation(): array
    {
        $cluster = static::getCluster();

        return $cluster
            ? $this->generateNavigationItems($cluster::getClusteredComponents())
            : [];
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $query = OfferResource::getEloquentQuery()
            ->with(['partner', 'lines.product', 'manufacturingManagedBy', 'manufacturingEntries.productionTask'])
            ->orderByDesc('confirmed_at');

        $toManage = (clone $query)->whereNull('manufacturing_managed_at')->get();
        $history = (clone $query)->whereNotNull('manufacturing_managed_at')->get();

        return [
            'orders'        => $this->activeTab === 'history' ? $history : $toManage,
            'toManageCount' => $toManage->count(),
            'historyCount'  => $history->count(),
        ];
    }

    public function isComplete(Order $order): bool
    {
        return ManufacturingFlow::orderIsComplete($order);
    }

    public function manageUrl(Order $order): string
    {
        return OfferResource::getUrl('manage', ['record' => $order]);
    }
}
