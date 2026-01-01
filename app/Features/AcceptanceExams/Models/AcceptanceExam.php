<?php

namespace App\Features\AcceptanceExams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'time_limit',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_answers',
        'is_active',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_answers' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(AcceptanceExamQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(AcceptanceExamAttempt::class);
    }

    public function getStudentAttempts(int $studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->get();
    }

    public function canStudentAttempt(int $studentId): bool
    {
        $attemptCount = $this->attempts()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->count();

        return $attemptCount < $this->max_attempts;
    }
}
