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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            
            // User who performed the action
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Action performed
            $table->string('action', 50)->index();
            
            // Auditable entity
            $table->string('auditable_type', 100)->index();
            $table->unsignedBigInteger('auditable_id')->index();
            
            // Additional context
            $table->string('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable(); // IP, user agent, etc.
            
            // Request context
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method', 10)->nullable();
            
            // Batch operations
            $table->string('batch_id', 36)->nullable()->index();
            
            // Tags for categorization
            $table->json('tags')->nullable();
            
            $table->timestamps();
            
            // Composite indexes for performance
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'action']);
            $table->index(['action', 'created_at']);
            $table->index(['auditable_type', 'action']);
            $table->index(['created_at', 'action']);
            
            // Partitioning friendly index
            $table->index(['created_at', 'user_id', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
