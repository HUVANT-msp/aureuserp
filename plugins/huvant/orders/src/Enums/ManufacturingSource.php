<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ManufacturingSource: string implements HasColor, HasLabel
{
    case Stock = 'stock';
    case Production = 'production';

    public function getLabel(): string
    {
        return __('huvant-orders::manufacturing.source_'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Stock      => 'success',
            self::Production => 'warning',
        };
    }
}
