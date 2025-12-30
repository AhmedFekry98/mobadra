<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoQuizOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_quiz_question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(VideoQuizQuestion::class, 'video_quiz_question_id');
    }
}
