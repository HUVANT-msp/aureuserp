<?php

use Huvant\Orders\Support\OrdersSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // What can be rented, by kind (e.g. "Renal biopsy torso"), with how many physical units exist.
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
