<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->string('name');
            $table->enum('track', ['online', 'offline']);
            $table->string('lab')->nullable();
            $table->string('governorate', 100);
            $table->string('project_title')->nullable();
            $table->text('project_description')->nullable();
            $table->decimal('phase4_score', 8, 2)->default(0);
            $table->decimal('hackathon_score', 8, 2)->default(0);
            $table->decimal('total_score', 8, 2)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->timestamps();

            $table->index(['competition_id', 'governorate']);
            $table->index(['competition_id', 'rank']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_teams');
    }
};
