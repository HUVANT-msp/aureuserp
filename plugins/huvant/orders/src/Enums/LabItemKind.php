<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasLabel;

/** How the lab classifies what it keeps in stock. */
enum LabItemKind: string implements HasLabel
{
    case Substance = 'substance';
    case Consumable = 'consumable';
    case Cleaning = 'cleaning';
    case Instrument = 'instrument';
    case Ppe = 'ppe';

    public function getLabel(): string
    {
        return match ($this) {
            self::Substance  => 'Substance',
            self::Consumable => 'Consumable',
            self::Cleaning   => 'Cleaning product',
            self::Instrument => 'Instrument',
            self::Ppe        => 'PPE',
        };
    }
}
