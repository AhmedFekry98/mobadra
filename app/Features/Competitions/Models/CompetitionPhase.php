<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'phase_number',
        'title',
        'title_ar',
        'description',
        'status',
        'start_date',
        'end_date',
        'max_points',
    ];

    protected $casts = [
        'phase_number' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_points' => 'integer',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
