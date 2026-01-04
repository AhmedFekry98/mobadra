<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('level_order')->default(1);
            $table->unsignedInteger('capacity');
            $table->string('course_slug')->nullable();
            $table->timestamps();
            $table->index(['competition_id', 'level_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_levels');
    }
};
