<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phase2_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('competition_participants')->cascadeOnDelete();
            $table->string('video_url', 500);
            $table->timestamp('submitted_at')->useCurrent();
            $table->decimal('idea_clarity', 5, 2)->default(0);
            $table->decimal('technical_understanding', 5, 2)->default(0);
            $table->decimal('logic_analysis', 5, 2)->default(0);
            $table->decimal('presentation_communication', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->text('feedback')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->index('participant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phase2_submissions');
    }
};
