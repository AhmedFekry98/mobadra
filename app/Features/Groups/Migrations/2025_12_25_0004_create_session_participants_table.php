<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جدول لربط الطالب بالحصة مع بيانات Zoom الخاصة به
     */
    public function up(): void
    {
        Schema::create('session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('group_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('zoom_registrant_id')->nullable(); // Zoom registrant ID للطالب
            $table->string('zoom_participant_id')->nullable(); // Zoom participant ID
            $table->string('join_url')->nullable(); // رابط الانضمام الخاص بالطالب
            $table->timestamp('first_join_time')->nullable(); // أول وقت دخول
            $table->timestamp('last_leave_time')->nullable(); // آخر وقت خروج
            $table->integer('total_duration')->default(0); // إجمالي وقت الحضور بالدقائق
            $table->enum('status', ['registered', 'joined', 'left', 'absent'])->default('registered');
            $table->json('join_leave_logs')->nullable(); // سجل كل مرات الدخول والخروج
            $table->timestamps();

            $table->unique(['session_id', 'user_id']);
            $table->index(['session_id']);
            $table->index(['user_id']);
            $table->index(['zoom_registrant_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_participants');
    }
};
