<?php

namespace App\Features\Competitions\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'user_id',
        'governorate',
        'status',
        'phase1_score',
        'phase2_score',
        'phase3_score',
        'total_score',
        'rank',
        'team_id',
    ];

    protected $casts = [
        'phase1_score' => 'decimal:2',
        'phase2_score' => 'decimal:2',
        'phase3_score' => 'decimal:2',
        'total_score' => 'decimal:2',
        'rank' => 'integer',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(CompetitionTeam::class, 'team_id');
    }

    public function teamMembership()
    {
        return $this->hasOne(CompetitionTeamMember::class, 'participant_id');
    }

    public function phase2Submission()
    {
        return $this->hasOne(Phase2Submission::class, 'participant_id');
    }

    public function calculateTotalScore(): void
    {
        $this->update([
            'total_score' => $this->phase1_score + $this->phase2_score + $this->phase3_score,
        ]);
    }

    public function getTier(): string
    {
        $competition = $this->competition;
        $totalParticipants = $competition->qualifiedParticipants()->count();

        if ($totalParticipants === 0) {
            return 'Emerging';
        }

        $rank = $this->rank ?? $totalParticipants;
        $percentile = ($rank / $totalParticipants) * 100;

        if ($percentile <= 33) {
            return 'High';
        } elseif ($percentile <= 67) {
            return 'Mid';
        }

        return 'Emerging';
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeByGovernorate($query, string $governorate)
    {
        return $query->where('governorate', $governorate);
    }
}
