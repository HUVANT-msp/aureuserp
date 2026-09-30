<?php

namespace Webkul\Contact\Filament\Resources\PartnerResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Contact\Filament\Resources\PartnerResource;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners as BaseListPartners;
use Webkul\TableViews\Filament\Components\PresetView;

/** Contacts are organised in three lists only: the team, external people, and companies. */
class ListPartners extends BaseListPartners
{
    protected static string $resource = PartnerResource::class;

    public const INTERNAL_TAG = 'Team Huvant';

    public const EXTERNAL_TAG = 'Esterno';

    public function getPresetTableViews(): array
    {
        $tagged = fn (string $tag) => fn (Builder $query) => $query->whereHas('tags', fn (Builder $q) => $q->where('name', $tag));

        return [
            'internal' => PresetView::make('Internal')
                ->icon('heroicon-s-user-group')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $tagged(self::INTERNAL_TAG)($query->where('account_type', AccountType::INDIVIDUAL))),

            'external' => PresetView::make('External')
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $tagged(self::EXTERNAL_TAG)($query->where('account_type', AccountType::INDIVIDUAL))),

            'companies' => PresetView::make('Companies')
                ->icon('heroicon-s-building-office')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::COMPANY)),
        ];
    }

    public function hasDefaultTableView(): bool
    {
        return false;
    }

    public function hasTableViewsMenu(): bool
    {
        return false;
    }
}
