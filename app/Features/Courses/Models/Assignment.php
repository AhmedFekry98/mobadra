<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Assignment extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function getStudentSubmission(int $studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && now()->gt($this->due_date);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('assignment_files')
            ->useDisk('media');
    }
}
