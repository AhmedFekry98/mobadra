<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionHackathonDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'day_number',
        'title',
        'title_ar',
        'description',
        'date',
        'status',
        'level',
        'teams_count',
    ];

    protected $casts = [
        'day_number' => 'integer',
        'date' => 'date',
        'teams_count' => 'integer',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
