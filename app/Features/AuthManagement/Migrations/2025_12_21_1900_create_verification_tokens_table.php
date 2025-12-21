<?php

use App\Enums\VerificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('type', VerificationType::values());
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['email', 'type']);
            $table->index(['phone', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_tokens');
    }
};
