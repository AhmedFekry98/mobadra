<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_url',
        'video_provider',
        'duration',
        'thumbnail_url',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }
}
