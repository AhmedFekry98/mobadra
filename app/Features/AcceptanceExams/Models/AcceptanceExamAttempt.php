<?php

namespace App\Features\AcceptanceExams\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'acceptance_exam_id',
        'student_id',
        'attempt_number',
        'status',
        'started_at',
        'completed_at',
        'score',
        'total_points',
        'percentage',
        'passed',
    ];

    protected $casts = [
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'total_points' => 'integer',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
    ];

    public function acceptanceExam()
    {
        return $this->belongsTo(AcceptanceExam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(AcceptanceExamAnswer::class, 'attempt_id');
    }

    public function calculateScore(): void
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($this->answers as $answer) {
            $totalPoints += $answer->question->points;
            $earnedPoints += $answer->points_earned;
        }

        $this->score = $earnedPoints;
        $this->total_points = $totalPoints;
        $this->percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $this->passed = $this->percentage >= $this->acceptanceExam->passing_score;
        $this->save();
    }
}
