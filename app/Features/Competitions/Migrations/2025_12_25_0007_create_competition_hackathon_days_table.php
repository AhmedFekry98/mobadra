<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_hackathon_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->tinyInteger('day_number');
            $table->string('title');
            $table->string('title_ar');
            $table->text('description')->nullable();
            $table->date('date');
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->enum('level', ['governorate', 'national', 'final']);
            $table->unsignedInteger('teams_count')->default(0);
            $table->timestamps();

            $table->index(['competition_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_hackathon_days');
    }
};
