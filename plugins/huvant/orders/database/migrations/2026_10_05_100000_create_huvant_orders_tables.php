<?php

use Huvant\Orders\Support\OrdersSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Progressive counters per document type and year (offers, orders, delivery notes).
        Schema::create('huvant_document_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10);
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['prefix', 'year']);
        });

        // An offer and, once confirmed, the order it becomes: one record, two numbers.
        Schema::create('huvant_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique();
            $table->string('order_number', 30)->nullable()->unique();
            $table->string('state', 20)->default('draft')->index();
            $table->string('supply_type', 30)->default('sale');
            $table->string('fulfilment', 20)->default('courier');
            // Null only for production for stock.
            $table->unsignedBigInteger('partner_id')->nullable()->index();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->unsignedBigInteger('hand_delivery_user_id')->nullable();
            $table->string('event', 160)->nullable();
            $table->string('subject', 255)->nullable();
            $table->date('offer_date');
            $table->date('validity_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            // Rentals and goods that come back (loan, on approval, samples).
            $table->date('rental_starts_on')->nullable();
            $table->date('rental_ends_on')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->string('payment_terms', 255)->nullable();
            $table->decimal('vat_rate', 5, 2)->default(22);
            $table->text('notes')->nullable();
            $table->string('closing_state', 20)->default('open');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Plugin migrations can run before the tables they point to (fresh installs, tests).
            if (Schema::hasTable('partners_partners')) {
                $table->foreign('partner_id')->references('id')->on('partners_partners')->restrictOnDelete();
                $table->foreign('contact_id')->references('id')->on('partners_partners')->nullOnDelete();
                $table->foreign('delivery_address_id')->references('id')->on('partners_partners')->nullOnDelete();
            }
            if (Schema::hasTable('users')) {
                $table->foreign('hand_delivery_user_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasTable('companies')) {
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            }
        });

        Schema::create('huvant_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('huvant_orders')->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->unsignedBigInteger('product_id')->index();
            $table->string('description', 255)->nullable();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('discount', 5, 2)->default(0);
            // The manufacturing order raised for this line when the order was confirmed.
            $table->unsignedBigInteger('manufacturing_order_id')->nullable()->index();
            $table->timestamps();

            if (Schema::hasTable('products_products')) {
                $table->foreign('product_id')->references('id')->on('products_products')->restrictOnDelete();
            }
            if (Schema::hasTable('manufacturing_orders')) {
                $table->foreign('manufacturing_order_id')->references('id')->on('manufacturing_orders')->nullOnDelete();
            }
        });

        OrdersSchema::ensureColumns();
    }

    public function down(): void
    {
        OrdersSchema::dropColumns();

        Schema::dropIfExists('huvant_order_lines');
        Schema::dropIfExists('huvant_orders');
        Schema::dropIfExists('huvant_document_numbers');
    }
};
