<?php

namespace App\Features\AuthManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $table = 'password_reset_tokens';
    protected $fillable = [
        'email',
        'verify_token',
        'expires_at',
    ];

}
