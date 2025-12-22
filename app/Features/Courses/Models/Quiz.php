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
}
