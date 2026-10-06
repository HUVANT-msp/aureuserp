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
        return match ($this) {
            self::Product  => 'Product',
            self::Material => 'Raw material',
            self::Rental   => 'Rental',
            self::Service  => 'Service',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Product  => 'Made or sold by Huvant: pads, kits, simulators. Tracked by lot.',
            self::Material => 'Used to make products: reagents, consumables, PPE. Lab stock, by lot.',
            self::Rental   => 'Lent for a period: torsos, stations. Units are the pieces in stock.',
            self::Service  => 'No stock: support staff, shipping costs.',
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
