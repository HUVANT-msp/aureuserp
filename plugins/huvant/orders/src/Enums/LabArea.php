<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasLabel;

/** Production material is what recipes use; R&D keeps its own packages. */
enum LabArea: string implements HasLabel
{
    case Production = 'production';
    case Research = 'research';

    public function getLabel(): string
    {
        return match ($this) {
            self::Production => __('huvant-orders::lab.production'),
            self::Research   => __('huvant-orders::lab.research'),
        };
    }
}
