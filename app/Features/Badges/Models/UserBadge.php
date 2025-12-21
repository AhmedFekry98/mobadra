<?php

namespace App\Features\Badges\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class UserBadge extends Model
{
    use HasFactory ,Auditable;

    protected $fillable = [
        'user_id',
        'badge_id',
        'awarded_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
