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
        Schema::create('content_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lesson_content_id')->constrained('lesson_contents')->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete(); // ربط بالجروب
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->integer('watch_time')->default(0); // بالثواني - الوقت الفعلي للمشاهدة
            $table->integer('last_position')->default(0); // بالثواني - آخر موضع في الفيديو
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_content_id', 'group_id']);
            $table->index(['user_id']);
            $table->index(['lesson_content_id']);
            $table->index(['group_id']);
            $table->index(['is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_progress');
    }
};
