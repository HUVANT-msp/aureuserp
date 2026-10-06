<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;

class ListRecipes extends ListRecords
{
    protected static string $resource = RecipeResource::class;

    public function getTitle(): string
    {
        return __('huvant-orders::lab.recipes');
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label(__('huvant-orders::lab.new_product'))];
    }
}
