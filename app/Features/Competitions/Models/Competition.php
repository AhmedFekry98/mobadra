<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'start_date',
        'end_date',
        'status',
        'total_participants',
        'qualified_count',
        'teams_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_participants' => 'integer',
        'qualified_count' => 'integer',
        'teams_count' => 'integer',
    ];

    public function phases()
    {
        return $this->hasMany(CompetitionPhase::class)->orderBy('phase_number');
    }

    public function participants()
    {
        return $this->hasMany(CompetitionParticipant::class);
    }

    public function teams()
    {
        return $this->hasMany(CompetitionTeam::class);
    }

    public function judges()
    {
        return $this->hasMany(CompetitionJudge::class);
    }

    public function hackathonDays()
    {
        return $this->hasMany(CompetitionHackathonDay::class)->orderBy('day_number');
    }

    public function qualifiedParticipants()
    {
        return $this->participants()->where('status', 'qualified');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    public function updateCounts(): void
    {
        $this->update([
            'total_participants' => $this->participants()->count(),
            'qualified_count' => $this->qualifiedParticipants()->count(),
            'teams_count' => $this->teams()->count(),
        ]);
    }
}
