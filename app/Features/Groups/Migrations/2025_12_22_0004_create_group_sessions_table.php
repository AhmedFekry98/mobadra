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
        Schema::create('group_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('topic')->nullable();
            $table->foreignId('lesson_id')->nullable()->constrained('lessons')->nullOnDelete();
            $table->boolean('is_cancelled')->default(false);
            $table->string('cancellation_reason')->nullable();
            $table->string('meeting_provider')->nullable(); // zoom, google_meet, teams
            $table->string('meeting_id')->nullable();
            $table->string('meeting_password')->nullable();
            $table->string('moderator_link')->nullable(); // رابط المدرس/Host
            $table->string('attendee_link')->nullable();  // رابط الطلاب/Join
            $table->timestamps();

            $table->index(['group_id']);
            $table->index(['session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_sessions');
    }
};
