<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'track',
        'lab',
        'governorate',
        'project_title',
        'project_description',
        'phase4_score',
        'hackathon_score',
        'total_score',
        'rank',
    ];

    protected $casts = [
        'phase4_score' => 'decimal:2',
        'hackathon_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'rank' => 'integer',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function members()
    {
        return $this->hasMany(CompetitionTeamMember::class, 'team_id');
    }

    public function participants()
    {
        return $this->hasManyThrough(
            CompetitionParticipant::class,
            CompetitionTeamMember::class,
            'team_id',
            'id',
            'id',
            'participant_id'
        );
    }

    public function evaluations()
    {
        return $this->hasMany(TeamEvaluation::class, 'team_id');
    }

    public function calculateTotalScore(): void
    {
        $avgEvaluation = $this->evaluations()->avg('total_score') ?? 0;
        $this->update([
            'total_score' => $this->phase4_score + $this->hackathon_score + $avgEvaluation,
        ]);
    }
}
