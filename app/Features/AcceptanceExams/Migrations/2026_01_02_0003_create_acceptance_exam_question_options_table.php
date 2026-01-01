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
        Schema::create('acceptance_exam_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('acceptance_exam_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acceptance_exam_question_options');
    }
};
