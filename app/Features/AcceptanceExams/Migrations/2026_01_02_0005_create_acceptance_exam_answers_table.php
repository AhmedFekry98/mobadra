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
        Schema::create('acceptance_exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('acceptance_exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('acceptance_exam_questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('acceptance_exam_question_options')->nullOnDelete();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('points_earned')->default(0);
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
            $table->index(['attempt_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptance_exam_answers');
    }
};
