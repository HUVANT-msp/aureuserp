<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Huvant\Orders\Support\LabCatalogue;

class CreateRecipe extends CreateRecord
{
    protected static string $resource = RecipeResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public function getTitle(): string
    {
        return __('huvant-orders::lab.new_product');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return LabCatalogue::productDefaults($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
