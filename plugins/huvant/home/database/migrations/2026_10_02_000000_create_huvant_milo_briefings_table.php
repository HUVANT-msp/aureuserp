<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('huvant_milo_briefings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('slot', 12);
            $table->string('headline', 255)->nullable();
            $table->text('summary')->nullable();
            $table->json('focus')->nullable();
            $table->json('heads_up')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('generated_at')->index();
            $table->timestamps();

            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_milo_briefings');
    }
};
