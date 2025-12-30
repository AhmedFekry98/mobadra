<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    public function attempt()
    {
        return $this->belongsTo(VideoQuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(VideoQuizQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(VideoQuizOption::class, 'selected_option_id');
    }

    public function checkAnswer(): void
    {
        $question = $this->question;
        $correctOption = $question->correctOption();

        $this->is_correct = $correctOption && $this->selected_option_id == $correctOption->id;
        $this->points_earned = $this->is_correct ? $question->points : 0;
        $this->save();
    }
}
