<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('huvant_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 16)->index();          // meeting, remote, travel, away, focus, other
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->boolean('all_day')->default(false);
            $table->boolean('private')->default(false);
            $table->unsignedBigInteger('organizer_id')->nullable()->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->timestamps();

            if (Schema::hasTable('users')) {
                $table->foreign('organizer_id')->references('id')->on('users')->nullOnDelete();
            }
            if (Schema::hasTable('projects_projects')) {
                $table->foreign('project_id')->references('id')->on('projects_projects')->nullOnDelete();
            }
        });

        Schema::create('huvant_calendar_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('huvant_calendar_events')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('response', 12)->default('pending'); // pending, accepted, declined, tentative
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);

            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_calendar_attendees');
        Schema::dropIfExists('huvant_calendar_events');
    }
};
