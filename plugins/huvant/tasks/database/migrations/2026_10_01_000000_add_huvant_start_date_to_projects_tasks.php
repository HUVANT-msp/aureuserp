<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Planned start, for the timeline (a task otherwise only has a deadline).
    public function up(): void
    {
        if (Schema::hasTable('projects_tasks') && ! Schema::hasColumn('projects_tasks', 'huvant_start_date')) {
            Schema::table('projects_tasks', function (Blueprint $table) {
                $table->date('huvant_start_date')->nullable()->after('deadline');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects_tasks', 'huvant_start_date')) {
            Schema::table('projects_tasks', fn (Blueprint $table) => $table->dropColumn('huvant_start_date'));
        }
    }
};
