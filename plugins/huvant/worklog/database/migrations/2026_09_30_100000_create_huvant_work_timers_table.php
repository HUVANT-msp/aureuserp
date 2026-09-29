<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // At most one running timer per person.
        Schema::create('huvant_work_timers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->unsignedBigInteger('task_id')->index();
            $table->timestamp('started_at');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            // Plugin migrations can run before the tables they point to (fresh installs, tests).
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            if (Schema::hasTable('projects_tasks')) {
                $table->foreign('task_id')->references('id')->on('projects_tasks')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_work_timers');
    }
};
