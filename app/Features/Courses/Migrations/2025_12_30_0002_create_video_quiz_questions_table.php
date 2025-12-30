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
        Schema::create('video_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_quiz_id')->constrained('video_quizzes')->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['single_choice', 'true_false'])->default('single_choice');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->integer('timestamp_seconds')->nullable();
            $table->text('explanation')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['video_quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_quiz_questions');
    }
};
