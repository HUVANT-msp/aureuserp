<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('huvant_project_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->timestamps();
            $table->unique(['project_id', 'team_id']);

            // Plugin migrations can run before the projects plugin's own (fresh
            // installs, test bootstrap): link the tables only when they exist.
            if (Schema::hasTable('projects_projects')) {
                $table->foreign('project_id')->references('id')->on('projects_projects')->cascadeOnDelete();
            }
            if (Schema::hasTable('teams')) {
                $table->foreign('team_id')->references('id')->on('teams')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_project_teams');
    }
};
