<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructions',
        'due_date',
        'max_score',
        'allow_late_submission',
        'allowed_file_types',
        'max_file_size',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'max_score' => 'integer',
        'allow_late_submission' => 'boolean',
        'allowed_file_types' => 'array',
        'max_file_size' => 'integer',
    ];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }
}
