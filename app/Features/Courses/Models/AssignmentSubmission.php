<?php

namespace App\Features\Courses\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AssignmentSubmission extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'status',
        'submitted_at',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'is_late',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'integer',
        'is_late' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->useDisk('media');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function submit(): void
    {
        $this->status = 'submitted';
        $this->submitted_at = now();

        // Check if late
        if ($this->assignment->due_date && now()->gt($this->assignment->due_date)) {
            $this->is_late = true;
        }

        $this->save();
    }

    public function grade(int $score, string $feedback, int $graderId): void
    {
        $this->score = $score;
        $this->feedback = $feedback;
        $this->graded_by = $graderId;
        $this->graded_at = now();
        $this->status = 'graded';
        $this->save();
    }
}
