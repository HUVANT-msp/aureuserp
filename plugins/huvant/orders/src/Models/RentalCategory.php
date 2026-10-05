<?php

namespace Huvant\Orders\Models;

use Illuminate\Database\Eloquent\Model;

class RentalCategory extends Model
{
    protected $table = 'huvant_rental_categories';

    protected $fillable = ['name', 'units', 'color', 'notes'];

    protected function casts(): array
    {
        return ['units' => 'integer'];
    }
}
