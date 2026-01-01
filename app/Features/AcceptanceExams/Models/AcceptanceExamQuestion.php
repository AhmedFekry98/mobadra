<?php

namespace App\Features\AcceptanceExams\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'acceptance_exam_id',
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

    public function acceptanceExam()
    {
        return $this->belongsTo(AcceptanceExam::class);
    }

    public function options()
    {
        return $this->hasMany(AcceptanceExamQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    public function answers()
    {
        return $this->hasMany(AcceptanceExamAnswer::class, 'question_id');
    }
}
