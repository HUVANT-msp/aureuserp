<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('huvant_orders', function (Blueprint $table) {
            $table->dateTime('manufacturing_managed_at')->nullable()->after('confirmed_at')->index();
            $table->unsignedBigInteger('manufacturing_managed_by')->nullable()->after('manufacturing_managed_at');
        });

        Schema::table('huvant_order_lines', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->nullable()->after('quantity');
            $table->unsignedInteger('production_quantity')->nullable()->after('stock_quantity');
            $table->dateTime('manufacturing_managed_at')->nullable()->after('production_quantity');
            $table->unsignedBigInteger('manufacturing_managed_by')->nullable()->after('manufacturing_managed_at');
        });

        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('product_id')->index();
            $table->unsignedBigInteger('order_line_id')->nullable()->after('order_id')->index();
        });

        Schema::create('huvant_production_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('huvant_orders')->cascadeOnDelete();
            $table->foreignId('order_line_id')->constrained('huvant_order_lines')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('completed_quantity')->default(0);
            $table->string('status', 20)->default('pending')->index();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedBigInteger('managed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('order_line_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_production_tasks');

        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'order_line_id']);
        });

        Schema::table('huvant_order_lines', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'production_quantity', 'manufacturing_managed_at', 'manufacturing_managed_by']);
        });

        Schema::table('huvant_orders', function (Blueprint $table) {
            $table->dropColumn(['manufacturing_managed_at', 'manufacturing_managed_by']);
        });
    }
};
