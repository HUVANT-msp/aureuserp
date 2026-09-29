<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('huvant_document_folders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->index();
            $table->foreignId('parent_id')->nullable()->constrained('huvant_document_folders')->restrictOnDelete();
            $table->string('name', 160);
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();

            // Plugin migrations can run before the tables they point to (fresh installs, tests).
            if (Schema::hasTable('projects_projects')) {
                $table->foreign('project_id')->references('id')->on('projects_projects')->cascadeOnDelete();
            }
            if (Schema::hasTable('users')) {
                $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        // A document is a file or a note, in a project (optionally in a folder) or on one of its tasks.
        Schema::create('huvant_documents', function (Blueprint $table) {
            $table->id();
            // Null only for files of a task outside any project.
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->foreignId('folder_id')->nullable()->constrained('huvant_document_folders')->restrictOnDelete();
            $table->unsignedBigInteger('task_id')->nullable()->index();
            $table->string('type', 10);
            $table->string('title', 255);
            $table->longText('body')->nullable();
            $table->string('disk', 40)->nullable();
            $table->string('path', 500)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            if (Schema::hasTable('projects_projects')) {
                $table->foreign('project_id')->references('id')->on('projects_projects')->cascadeOnDelete();
            }
            if (Schema::hasTable('projects_tasks')) {
                $table->foreign('task_id')->references('id')->on('projects_tasks')->nullOnDelete();
            }
            if (Schema::hasTable('users')) {
                $table->foreign('creator_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('huvant_documents');
        Schema::dropIfExists('huvant_document_folders');
    }
};
