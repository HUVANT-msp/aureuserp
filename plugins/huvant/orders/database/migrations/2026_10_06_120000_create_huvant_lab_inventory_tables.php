<?php

use Huvant\Orders\Support\OrdersSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // The lab inventory on its own terms: recipes, packages of raw materials in the lab, finished
    // pieces with their identification code and the material lots that went into them.
    public function up(): void
    {
        Schema::create('huvant_recipe_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('material_id')->index();
            $table->decimal('quantity', 15, 4);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // One row per package (or container) in the lab.
        Schema::create('huvant_material_lots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->index();
            $table->string('lot_number', 80);
            $table->date('expiry_date')->nullable();
            $table->date('received_on')->nullable();
            $table->string('area', 20)->default('production');
            $table->decimal('initial_quantity', 15, 4);
            $table->decimal('remaining_quantity', 15, 4);
            $table->dateTime('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
        });

        // One row per finished piece.
        Schema::create('huvant_product_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('code', 40)->unique();
            $table->date('production_date');
            $table->date('expiry_date')->nullable();
            $table->string('status', 20)->default('in_lab')->index();
            $table->date('status_since')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
        });

        Schema::create('huvant_product_unit_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_unit_id')->constrained('huvant_product_units')->cascadeOnDelete();
            $table->unsignedBigInteger('material_id');
            $table->foreignId('material_lot_id')->nullable()->constrained('huvant_material_lots')->nullOnDelete();
            // Kept as written, should the lot row ever go.
            $table->string('lot_number', 80);
            $table->decimal('quantity', 15, 4);
            $table->timestamps();
        });

        OrdersSchema::ensureColumns();

        if (! Schema::hasColumn('products_products', 'huvant_package_unit')) {
            return;
        }

        // Packages and minimums of the imported materials, in the package's own unit.
        DB::table('products_products')
            ->whereNotNull('huvant_package_uom_id')
            ->whereNull('huvant_package_unit')
            ->update(['huvant_package_unit' => DB::raw('(select name from unit_of_measures u where u.id = products_products.huvant_package_uom_id)')]);

        if (Schema::hasTable('inventories_order_points')) {
            foreach (DB::table('inventories_order_points')->select('product_id', DB::raw('min(product_min_qty) as minimum'))->groupBy('product_id')->get() as $point) {
                DB::table('products_products')->where('id', $point->product_id)->whereNull('huvant_min_quantity')->update(['huvant_min_quantity' => $point->minimum]);
            }
        }

        if (Schema::hasTable('manufacturing_bill_of_material_lines')) {
            foreach (DB::table('manufacturing_bills_of_materials')->where('type', 'normal')->orderBy('id')->get()->unique('product_id') as $bom) {
                foreach (DB::table('manufacturing_bill_of_material_lines')->where('bill_of_material_id', $bom->id)->orderBy('sort')->get() as $line) {
                    DB::table('huvant_recipe_lines')->insert([
                        'product_id' => $bom->product_id, 'material_id' => $line->product_id, 'quantity' => $line->quantity,
                        'sort'       => (int) $line->sort, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_product_unit_materials');
        Schema::dropIfExists('huvant_product_units');
        Schema::dropIfExists('huvant_material_lots');
        Schema::dropIfExists('huvant_recipe_lines');
    }
};
