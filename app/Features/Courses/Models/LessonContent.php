<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'content_type',
        'contentable_type',
        'contentable_id',
        'title',
        'description',
        'order',
        'duration',
        'is_required',
        'is_published',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration' => 'integer',
        'is_required' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function contentable()
    {
        return $this->morphTo();
    }
}
