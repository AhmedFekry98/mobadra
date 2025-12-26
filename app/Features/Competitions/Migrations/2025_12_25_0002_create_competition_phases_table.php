<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->tinyInteger('phase_number');
            $table->string('title');
            $table->string('title_ar');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('max_points')->default(0);
            $table->timestamps();

            $table->index(['competition_id', 'phase_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_phases');
    }
};
