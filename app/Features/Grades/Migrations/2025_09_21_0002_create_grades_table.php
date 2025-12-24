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
        // Create grades table
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Grade 4", "Grade 5"
            $table->string('code')->unique(); // e.g., "G4", "G5"
            $table->text('description')->nullable();
            $table->integer('min_age')->nullable(); // Minimum age for this grade
            $table->integer('max_age')->nullable(); // Maximum age for this grade
            $table->integer('order')->default(0); // For sorting
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
