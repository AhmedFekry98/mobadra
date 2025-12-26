<?php

namespace App\Features\Groups\Models;

use App\Features\Courses\Models\LessonContent;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentProgress extends Model
{
    use HasFactory;

    protected $table = 'content_progress';

    protected $fillable = [
        'user_id',
        'lesson_content_id',
        'group_id',
        'progress_percentage',
        'watch_time',
        'last_position',
        'is_completed',
        'completed_at',
        'last_watched_at',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'watch_time' => 'integer',
        'last_position' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessonContent()
    {
        return $this->belongsTo(LessonContent::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }

    public function updateProgress(int $percentage, int $position, int $watchTime): void
    {
        $this->update([
            'progress_percentage' => min($percentage, 100),
            'last_position' => $position,
            'watch_time' => $this->watch_time + $watchTime,
            'last_watched_at' => now(),
        ]);

        // Auto-complete if progress >= 90%
        if ($percentage >= 90 && !$this->is_completed) {
            $this->markAsCompleted();
        }
    }
}
