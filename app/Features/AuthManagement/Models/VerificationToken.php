<?php

namespace App\Features\AuthManagement\Models;

use App\Enums\VerificationType;
use Illuminate\Database\Eloquent\Model;

class VerificationToken extends Model
{
    protected $table = 'verification_tokens';

    protected $fillable = [
        'email',
        'phone',
        'type',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'type' => VerificationType::class,
    ];
}
