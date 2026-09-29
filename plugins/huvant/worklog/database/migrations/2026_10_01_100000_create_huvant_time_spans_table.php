<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // When an entry happened during the day (timer, or a start time given by hand): the timeline.
    public function up(): void
    {
        Schema::create('huvant_time_spans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timesheet_id')->unique();
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->timestamps();

            if (Schema::hasTable('analytic_records')) {
                $table->foreign('timesheet_id')->references('id')->on('analytic_records')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_time_spans');
    }
};
