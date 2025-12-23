<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'points',
        'order',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id');
    }
}
