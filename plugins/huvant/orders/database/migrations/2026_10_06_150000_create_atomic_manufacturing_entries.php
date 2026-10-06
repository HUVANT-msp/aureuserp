<?php

use Huvant\Orders\Support\ManufacturingFlow;
use Huvant\Orders\Support\ProductionProjects;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('huvant_orders', function (Blueprint $table) {
            $table->dateTime('manufacturing_completed_at')->nullable()->after('manufacturing_managed_by')->index();
        });

        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->unsignedTinyInteger('quality_rating')->nullable()->after('notes');
        });

        Schema::create('huvant_product_projects', function (Blueprint $table) {
            $table->id();
            // Product and project plugins can be installed after this package in a fresh ERP.
            $table->unsignedBigInteger('product_id')->unique();
            $table->unsignedBigInteger('project_id')->unique();
            $table->timestamps();
        });

        Schema::create('huvant_manufacturing_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('huvant_orders')->cascadeOnDelete();
            $table->foreignId('order_line_id')->constrained('huvant_order_lines')->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedInteger('position');
            $table->string('source', 20)->nullable()->index();
            $table->foreignId('product_unit_id')->nullable()->unique()->constrained('huvant_product_units')->nullOnDelete();
            $table->dateTime('managed_at')->nullable();
            $table->unsignedBigInteger('managed_by')->nullable();
            $table->timestamps();

            $table->unique(['order_line_id', 'position']);
        });

        Schema::table('huvant_production_tasks', function (Blueprint $table) {
            // Keep a non-unique index available for the existing order-line foreign key.
            $table->index('order_line_id', 'huvant_production_tasks_order_line_index');
        });

        Schema::table('huvant_production_tasks', function (Blueprint $table) {
            $table->dropUnique(['order_line_id']);
            $table->foreignId('manufacturing_entry_id')->nullable()->unique()->after('order_line_id')->constrained('huvant_manufacturing_entries')->cascadeOnDelete();
            $table->unsignedBigInteger('project_task_id')->nullable()->unique()->after('manufacturing_entry_id');
            $table->date('due_date')->nullable()->after('quantity')->index();
        });

        ProductionProjects::backfill();
        ManufacturingFlow::backfill();
    }

    public function down(): void
    {
        Schema::table('huvant_production_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manufacturing_entry_id');
            $table->dropColumn(['project_task_id', 'due_date']);
            $table->unique('order_line_id');
        });

        Schema::table('huvant_production_tasks', function (Blueprint $table) {
            $table->dropIndex('huvant_production_tasks_order_line_index');
        });

        Schema::dropIfExists('huvant_manufacturing_entries');
        Schema::dropIfExists('huvant_product_projects');

        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->dropColumn('quality_rating');
        });

        Schema::table('huvant_orders', function (Blueprint $table) {
            $table->dropColumn('manufacturing_completed_at');
        });
    }
};
