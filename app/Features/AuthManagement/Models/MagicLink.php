<?php

namespace App\Features\AuthManagement\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MagicLink extends Model
{
    use HasFactory;
    protected $table = 'magic_links';
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool {
        return !$this->used && $this->expires_at->isFuture();
    }
}
