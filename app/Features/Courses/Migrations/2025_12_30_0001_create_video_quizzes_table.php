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
        Schema::create('video_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_content_id')->constrained('video_contents')->cascadeOnDelete();
            $table->integer('max_questions')->default(3);
            $table->integer('passing_score')->default(60);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('video_content_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_quizzes');
    }
};
