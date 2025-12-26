<?php

namespace App\Features\Competitions\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'evaluator_id',
        'idea_strength',
        'implementation',
        'teamwork',
        'problem_solving',
        'final_presentation',
        'total_score',
        'feedback',
    ];

    protected $casts = [
        'idea_strength' => 'decimal:2',
        'implementation' => 'decimal:2',
        'teamwork' => 'decimal:2',
        'problem_solving' => 'decimal:2',
        'final_presentation' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function team()
    {
        return $this->belongsTo(CompetitionTeam::class, 'team_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function calculateTotalScore(): void
    {
        $total = $this->idea_strength + $this->implementation
               + $this->teamwork + $this->problem_solving + $this->final_presentation;

        $this->update(['total_score' => $total]);

        // Recalculate team's total score
        $this->team->calculateTotalScore();
    }
}
