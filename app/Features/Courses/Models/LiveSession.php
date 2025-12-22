<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_url',
        'meeting_provider',
        'start_time',
        'end_time',
        'max_participants',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'max_participants' => 'integer',
    ];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }
}
