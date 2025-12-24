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
        // جدول محتوى الدرس (Lesson Contents)
        Schema::create('lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('content_type');
            $table->string('contentable_type')->nullable();
            $table->unsignedBigInteger('contentable_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration')->default(0); // seconds
            $table->boolean('is_required')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['contentable_type', 'contentable_id']);
            $table->index('lesson_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_contents');
    }
};
