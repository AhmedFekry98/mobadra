<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'duration_minutes',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Lesson Contents
    public function contents()
    {
        return $this->hasMany(LessonContent::class);
    }

    public function quizzes()
    {
        return $this->selfLearningContents()
            ->where('contentable_type', Quiz::class)
            ->with('contentable');
    }

    public function assignments()
    {
        return $this->selfLearningContents()
            ->where('contentable_type', Assignment::class)
            ->with('contentable');
    }

    public function materials()
    {
        return $this->selfLearningContents()
            ->where('contentable_type', Material::class)
            ->with('contentable');
    }

    public function videos()
    {
        return $this->selfLearningContents()
            ->where('contentable_type', VideoContent::class)
            ->with('contentable');
    }
}
