<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('governorate', 100);
            $table->enum('status', ['registered', 'qualified', 'eliminated', 'pending'])->default('registered');
            $table->decimal('phase1_score', 8, 2)->default(0);
            $table->decimal('phase2_score', 8, 2)->default(0);
            $table->decimal('phase3_score', 8, 2)->default(0);
            $table->decimal('total_score', 8, 2)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->foreignId('team_id')->nullable()->constrained('competition_teams')->nullOnDelete();
            $table->timestamps();

            $table->unique(['competition_id', 'user_id'], 'unique_participant');
            $table->index(['competition_id', 'status']);
            $table->index(['competition_id', 'governorate']);
            $table->index(['competition_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_participants');
    }
};
