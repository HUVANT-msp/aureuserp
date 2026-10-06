<?php

namespace Huvant\Orders\Filament\Concerns;

use Filament\Actions\Action;
use Huvant\Orders\Models\ProductUnit;

/** "Which lots went into this piece", on every list of finished pieces. */
trait ShowsUnitMaterials
{
    protected function materialsAction(): Action
    {
        return Action::make('materials')
            ->label(__('huvant-orders::lab.materials'))
            ->icon('heroicon-o-beaker')
            ->color('gray')
            ->modalHeading(fn (ProductUnit $record): string => $record->code)
            ->modalDescription(fn (ProductUnit $record): string => __('huvant-orders::lab.produced_on', [
                'product' => $record->product?->name,
                'date'    => $record->production_date->format('d/m/Y'),
            ]))
            ->modalContent(fn (ProductUnit $record) => view('huvant-orders::filament.lab.unit-materials', [
                'materials' => $record->materials()->with(['material', 'lot'])->get(),
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('huvant-orders::lab.close'));
    }
}
