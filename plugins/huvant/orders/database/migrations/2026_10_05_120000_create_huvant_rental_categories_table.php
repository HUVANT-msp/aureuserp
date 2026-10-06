<?php

use Huvant\Orders\Support\OrdersSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Superseded on 2026-10-06 by rentals counted from stock (2026_10_06_100000_item_roles_replace_rental_categories).
    public function up(): void
    {
        Schema::create('huvant_rental_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->unsignedSmallInteger('units')->default(1);
            $table->string('color', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        OrdersSchema::ensureColumns();
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_rental_categories');
    }
};
