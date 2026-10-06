<?php

namespace Huvant\Orders\Filament\Clusters\RawMaterials\Resources\MaterialResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Huvant\Orders\Filament\Clusters\RawMaterials\Resources\MaterialResource;
use Huvant\Orders\Support\LabCatalogue;

class ManageMaterials extends ManageRecords
{
    protected static string $resource = MaterialResource::class;

    public function getTitle(): string
    {
        return __('huvant-orders::lab.raw_materials');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('huvant-orders::lab.new_raw_material'))
                ->mutateDataUsing(fn (array $data): array => LabCatalogue::materialDefaults($data)),
        ];
    }
}
