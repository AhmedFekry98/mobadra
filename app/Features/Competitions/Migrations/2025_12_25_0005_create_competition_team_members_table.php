<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('competition_teams')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('competition_participants')->cascadeOnDelete();
            $table->enum('role', ['Team Lead', 'Technical Lead', 'Research', 'Presentation', 'Documentation']);
            $table->enum('tier', ['High', 'Mid', 'Emerging']);
            $table->timestamps();

            $table->unique(['team_id', 'participant_id'], 'unique_member');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_team_members');
    }
};
