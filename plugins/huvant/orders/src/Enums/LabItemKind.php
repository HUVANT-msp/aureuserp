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
        return match (app()->getLocale()) {
            'it' => match ($this) {
                self::Substance  => 'Sostanza',
                self::Consumable => 'Materiale di consumo',
                self::Cleaning   => 'Prodotto per pulizia',
                self::Instrument => 'Strumento',
                self::Ppe        => 'DPI',
            },
            default => match ($this) {
                self::Substance  => 'Substance',
                self::Consumable => 'Consumable',
                self::Cleaning   => 'Cleaning product',
                self::Instrument => 'Instrument',
                self::Ppe        => 'PPE',
            },
        };
    }
}
