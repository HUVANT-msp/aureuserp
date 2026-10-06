<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

/** What an item is for the lab: it decides where it can be used and how it is stocked. */
enum ItemRole: string implements HasDescription, HasLabel
{
    case Product = 'product';
    case Material = 'material';
    case Rental = 'rental';
    case Service = 'service';

    public function getLabel(): string
    {
        return match (app()->getLocale()) {
            'it' => match ($this) {
                self::Product  => 'Prodotto',
                self::Material => 'Materia prima',
                self::Rental   => 'Noleggio',
                self::Service  => 'Servizio',
            },
            default => match ($this) {
                self::Product  => 'Product',
                self::Material => 'Raw material',
                self::Rental   => 'Rental',
                self::Service  => 'Service',
            },
        };
    }

    public function getDescription(): string
    {
        return match (app()->getLocale()) {
            'it' => match ($this) {
                self::Product  => 'Fabbricato o venduto da Huvant: pad, kit, simulatori. Tracciato per lotto.',
                self::Material => 'Utilizzato per la produzione: reagenti, consumabili, DPI. Giacenza di laboratorio, per lotto.',
                self::Rental   => 'Concesso a noleggio per un periodo: manichini, postazioni. Le unità corrispondono ai pezzi a magazzino.',
                self::Service  => 'Nessuna giacenza: personale di supporto, spese di spedizione.',
            },
            default => match ($this) {
                self::Product  => 'Made or sold by Huvant: pads, kits, simulators. Tracked by lot.',
                self::Material => 'Used to make products: reagents, consumables, PPE. Lab stock, by lot.',
                self::Rental   => 'Lent for a period: torsos, stations. Units are the pieces in stock.',
                self::Service  => 'No stock: support staff, shipping costs.',
            },
        };
    }

    /** Roles that can go on an offer. @return array<int, string> */
    public static function sellable(): array
    {
        return [self::Product->value, self::Rental->value, self::Service->value];
    }

    /** Roles that can go into a bill of materials. @return array<int, string> */
    public static function components(): array
    {
        return [self::Material->value, self::Product->value];
    }
}
