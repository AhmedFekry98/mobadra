<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_limit',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_answers',
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_answers' => 'boolean',
    ];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
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
