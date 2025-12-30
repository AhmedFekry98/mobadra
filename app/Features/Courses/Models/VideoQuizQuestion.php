<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_quiz_id',
        'question',
        'type',
        'points',
        'order',
        'timestamp_seconds',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
        'timestamp_seconds' => 'integer',
        'is_active' => 'boolean',
    ];

    public function videoQuiz()
    {
        return $this->belongsTo(VideoQuiz::class);
    }

    public function options()
    {
        return $this->hasMany(VideoQuizOption::class)->orderBy('order');
    }

    public function correctOption()
    {
        return $this->options()->where('is_correct', true)->first();
    }

    public function answers()
    {
        return $this->hasMany(VideoQuizAnswer::class, 'question_id');
    }
}
