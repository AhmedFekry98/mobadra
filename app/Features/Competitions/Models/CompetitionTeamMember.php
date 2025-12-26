<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'participant_id',
        'role',
        'tier',
    ];

    public function team()
    {
        return $this->belongsTo(CompetitionTeam::class, 'team_id');
    }

    public function participant()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'participant_id');
    }
}
