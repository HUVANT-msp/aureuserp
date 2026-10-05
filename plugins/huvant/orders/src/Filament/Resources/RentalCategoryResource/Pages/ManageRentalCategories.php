<?php

namespace Huvant\Orders\Filament\Resources\RentalCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Huvant\Orders\Filament\Resources\RentalCategoryResource;

class ManageRentalCategories extends ManageRecords
{
    protected static string $resource = RentalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
