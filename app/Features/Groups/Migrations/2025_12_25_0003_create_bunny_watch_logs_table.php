<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول لتسجيل مشاهدات Bunny Stream
     */
    public function up(): void
    {
        Schema::create('bunny_watch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lesson_content_id')->nullable()->constrained('lesson_contents')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->string('video_id'); // Bunny video GUID
            $table->string('video_library_id')->nullable(); // Bunny library ID
            $table->integer('watch_time')->default(0); // بالثواني
            $table->integer('video_duration')->nullable(); // مدة الفيديو الكاملة
            $table->integer('percentage_watched')->default(0); // النسبة المشاهدة
            $table->string('country_code')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('session_id')->nullable(); // Bunny session ID
            $table->json('raw_data')->nullable(); // البيانات الخام من Bunny
            $table->timestamp('watched_at')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['lesson_content_id']);
            $table->index(['group_id']);
            $table->index(['video_id']);
            $table->index(['watched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bunny_watch_logs');
    }
};
