<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Video Contents
        Schema::create('video_contents', function (Blueprint $table) {
            $table->id();
            $table->string('video_url');
            $table->string('video_url_en');
            $table->enum('video_provider', ['bunny']);
            $table->integer('duration')->default(0); // in seconds
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();
        });

        // Quizzes
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->default(60); // percentage
            $table->integer('max_attempts')->default(1);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_answers')->default(false);
            $table->timestamps();
        });

        // Assignments
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->text('instructions')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->boolean('allow_late_submission')->default(false);
            $table->json('allowed_file_types')->nullable(); // ['pdf', 'doc', 'docx']
            $table->integer('max_file_size')->nullable(); // in MB
            $table->timestamps();
        });

        // Materials (PDFs, documents, files) - files stored via Spatie Media Library
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('file_type')->nullable(); // pdf, doc, ppt, etc.
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('video_contents');
    }
};
