<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_judges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained('competitions')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('specialty');
            $table->string('avatar', 500)->nullable();
            $table->timestamps();

            $table->index('competition_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_judges');
    }
};
