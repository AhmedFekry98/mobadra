<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('competition_teams')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('idea_strength', 5, 2)->default(0);
            $table->decimal('implementation', 5, 2)->default(0);
            $table->decimal('teamwork', 5, 2)->default(0);
            $table->decimal('problem_solving', 5, 2)->default(0);
            $table->decimal('final_presentation', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->index('team_id');
            $table->index('evaluator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_evaluations');
    }
};
