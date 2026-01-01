<?php

namespace App\Features\AcceptanceExams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'text_answer',
        'is_correct',
        'points_earned',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    public function attempt()
    {
        return $this->belongsTo(AcceptanceExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(AcceptanceExamQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(AcceptanceExamQuestionOption::class, 'selected_option_id');
    }

    public function checkAnswer(): void
    {
        $question = $this->question;

        if (in_array($question->type, ['single_choice', 'multiple_choice', 'true_false'])) {
            $correctOptionIds = $question->correctOptions()->pluck('id')->toArray();
            $this->is_correct = in_array($this->selected_option_id, $correctOptionIds);
            $this->points_earned = $this->is_correct ? $question->points : 0;
        }

        $this->save();
    }
}
