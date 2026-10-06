<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Huvant\Orders\Support\Orders;

class EditRecipe extends EditRecord
{
    protected static string $resource = RecipeResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.recipe');
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->visible(fn (): bool => Orders::isAdmin())];
    }
}
