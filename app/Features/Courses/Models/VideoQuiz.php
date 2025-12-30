<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_content_id',
        'max_questions',
        'passing_score',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'max_questions' => 'integer',
        'passing_score' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function videoContent()
    {
        return $this->belongsTo(VideoContent::class);
    }

    public function questions()
    {
        return $this->hasMany(VideoQuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(VideoQuizAttempt::class);
    }

    public function getStudentAttempts(int $studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->get();
    }

    public function getStudentLatestAttempt(int $studentId): ?VideoQuizAttempt
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->latest()
            ->first();
    }

    public function hasStudentPassed(int $studentId): bool
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->where('passed', true)
            ->exists();
    }
}
