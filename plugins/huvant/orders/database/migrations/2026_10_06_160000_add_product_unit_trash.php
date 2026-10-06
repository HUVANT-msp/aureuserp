<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->text('deletion_note')->nullable()->after('quality_rating');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deletion_note');
            $table->softDeletes()->after('deleted_by');
        });
    }

    public function down(): void
    {
        Schema::table('huvant_product_units', function (Blueprint $table) {
            $table->dropColumn(['deletion_note', 'deleted_by', 'deleted_at']);
        });
    }
};
