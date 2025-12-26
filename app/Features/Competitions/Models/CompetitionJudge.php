<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionJudge extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'email',
        'specialty',
        'avatar',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
