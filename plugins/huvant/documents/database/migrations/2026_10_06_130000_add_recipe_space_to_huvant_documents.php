<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Documents can also belong to a recipe of the lab (a product), with its own folders.
    public function up(): void
    {
        Schema::table('huvant_document_folders', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->unsignedBigInteger('recipe_id')->nullable()->index()->after('project_id');
        });

        Schema::table('huvant_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('recipe_id')->nullable()->index()->after('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('huvant_documents', fn (Blueprint $table) => $table->dropColumn('recipe_id'));
        Schema::table('huvant_document_folders', fn (Blueprint $table) => $table->dropColumn('recipe_id'));
    }
};
