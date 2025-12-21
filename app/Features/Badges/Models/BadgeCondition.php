<?php

namespace App\Features\Badges\Models;

use App\Enums\Operator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class BadgeCondition extends Model
{
    use HasFactory ,Auditable;

    protected $fillable = [
        'badge_id',
        'field',
        'operator',
        'value',
    ];

    protected $casts = [
        'operator' => Operator::class,
    ];
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

}
