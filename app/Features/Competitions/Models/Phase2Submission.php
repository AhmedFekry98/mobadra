<?php

namespace App\Features\Competitions\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase2Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'video_url',
        'submitted_at',
        'idea_clarity',
        'technical_understanding',
        'logic_analysis',
        'presentation_communication',
        'total_score',
        'feedback',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'idea_clarity' => 'decimal:2',
        'technical_understanding' => 'decimal:2',
        'logic_analysis' => 'decimal:2',
        'presentation_communication' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function participant()
    {
        return $this->belongsTo(CompetitionParticipant::class, 'participant_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function calculateTotalScore(): void
    {
        $total = $this->idea_clarity + $this->technical_understanding
               + $this->logic_analysis + $this->presentation_communication;

        $this->update(['total_score' => $total]);

        // Update participant's phase2 score
        $this->participant->update(['phase2_score' => $total]);
        $this->participant->calculateTotalScore();
    }
}
