<?php

namespace App\Features\Groups\Services;

use App\Features\Courses\Models\LessonContent;
use App\Features\Groups\Models\ContentProgress;
use Illuminate\Database\Eloquent\Collection;

class ContentProgressService
{
    /**
     * Get or create progress record for a user and content
     */
    public function getOrCreateProgress(int $userId, int $lessonContentId, ?int $groupId = null): ContentProgress
    {
        return ContentProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_content_id' => $lessonContentId,
                'group_id' => $groupId,
            ],
            [
                'progress_percentage' => 0,
                'watch_time' => 0,
                'last_position' => 0,
                'is_completed' => false,
            ]
        );
    }

    /**
     * Update progress for a video
     */
    public function updateProgress(int $userId, int $lessonContentId, array $data, ?int $groupId = null): ContentProgress
    {
        $progress = $this->getOrCreateProgress($userId, $lessonContentId, $groupId);

        $progress->updateProgress(
            $data['progress_percentage'] ?? $progress->progress_percentage,
            $data['last_position'] ?? $progress->last_position,
            $data['watch_time'] ?? 0
        );

        return $progress->fresh();
    }

    /**
     * Get user's progress for a specific content
     */
    public function getUserProgress(int $userId, int $lessonContentId, ?int $groupId = null): ?ContentProgress
    {
        return ContentProgress::where('user_id', $userId)
            ->where('lesson_content_id', $lessonContentId)
            ->where('group_id', $groupId)
            ->first();
    }

    /**
     * Get all progress for a user in a group
     */
    public function getUserProgressByGroup(int $userId, int $groupId): Collection
    {
        return ContentProgress::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->with('lessonContent')
            ->get();
    }

    /**
     * Get all progress for a user in a course (via group)
     */
    public function getUserProgressByCourse(int $userId, int $courseId, ?int $groupId = null): Collection
    {
        $query = ContentProgress::where('user_id', $userId)
            ->whereHas('lessonContent.lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->with('lessonContent.lesson');

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        return $query->get();
    }

    /**
     * Calculate course completion percentage for a user in a group
     */
    public function getGroupCourseCompletionPercentage(int $userId, int $groupId, int $courseId): float
    {
        $totalContents = LessonContent::whereHas('lesson', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->where('is_required', true)->count();

        if ($totalContents === 0) {
            return 0;
        }

        $completedContents = ContentProgress::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('is_completed', true)
            ->whereHas('lessonContent.lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->whereHas('lessonContent', function ($q) {
                $q->where('is_required', true);
            })
            ->count();

        return round(($completedContents / $totalContents) * 100, 2);
    }

    /**
     * Mark content as completed
     */
    public function markAsCompleted(int $userId, int $lessonContentId, ?int $groupId = null): ContentProgress
    {
        $progress = $this->getOrCreateProgress($userId, $lessonContentId, $groupId);
        $progress->markAsCompleted();
        return $progress->fresh();
    }

    /**
     * Get all students progress for a group (for teachers)
     */
    public function getGroupStudentsProgress(int $groupId): Collection
    {
        return ContentProgress::where('group_id', $groupId)
            ->with(['user', 'lessonContent'])
            ->get();
    }

    /**
     * Get progress summary for all students in a group
     */
    public function getGroupProgressSummary(int $groupId, int $courseId): array
    {
        $students = \App\Features\Groups\Models\GroupStudent::where('group_id', $groupId)
            ->where('status', 'active')
            ->with('student')
            ->get();

        $summary = [];
        foreach ($students as $groupStudent) {
            $summary[] = [
                'student_id' => $groupStudent->student_id,
                'student_name' => $groupStudent->student->name ?? 'Unknown',
                'completion_percentage' => $this->getGroupCourseCompletionPercentage(
                    $groupStudent->student_id,
                    $groupId,
                    $courseId
                ),
            ];
        }

        return $summary;
    }
}
