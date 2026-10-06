<?php

use Huvant\Orders\Support\ItemRoles;
use Huvant\Orders\Support\OrdersSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Every item gets a role (product, raw material, rental, service); rentals are counted from the
    // pieces in stock, so rental categories go.
    public function up(): void
    {
        OrdersSchema::ensureColumns();

        if (Schema::hasColumn('products_products', 'huvant_role')) {
            ItemRoles::backfill();
        }

        if (Schema::hasColumn('products_products', 'huvant_rental_category_id')) {
            Schema::table('products_products', fn (Blueprint $table) => $table->dropColumn('huvant_rental_category_id'));
        }

        Schema::dropIfExists('huvant_rental_categories');
    }

    public function down(): void
    {
        Schema::create('huvant_rental_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->unsignedSmallInteger('units')->default(1);
            $table->string('color', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
