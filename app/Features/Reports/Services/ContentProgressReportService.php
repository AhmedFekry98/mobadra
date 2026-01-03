<?php

namespace App\Features\Reports\Services;

use App\Features\Groups\Models\ContentProgress;
use App\Features\Courses\Models\LessonContent;
use App\Features\SystemManagements\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ContentProgressReportService
{
    public function getAllStudentsContentProgressReport(array $filters): array
    {
        $query = ContentProgress::query()
            ->with(['user', 'lessonContent.lesson.course', 'group']);

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('lessonContent.lesson', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('lessonContent', function ($q) use ($filters) {
                $q->where('lesson_id', $filters['lesson_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $progress = $query->get();

        // Group by student
        $byStudent = $progress->groupBy('user_id');

        $studentsReport = $byStudent->map(function ($studentProgress, $userId) {
            $student = $studentProgress->first()->user;
            $total = $studentProgress->count();
            $completed = $studentProgress->where('is_completed', true)->count();
            $avgProgress = $studentProgress->avg('progress_percentage');
            $totalWatchTime = $studentProgress->sum('watch_time');

            return [
                'student_id' => $userId,
                'student_name' => $student?->name,
                'student_email' => $student?->email,
                'total_contents' => $total,
                'completed_contents' => $completed,
                'in_progress_contents' => $total - $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($avgProgress ?? 0, 2),
                'total_watch_time_seconds' => $totalWatchTime,
                'total_watch_time_formatted' => $this->formatWatchTime($totalWatchTime),
            ];
        })->sortByDesc('completion_rate')->values();

        $totalContents = $progress->count();
        $totalCompleted = $progress->where('is_completed', true)->count();

        return [
            'total_students' => $studentsReport->count(),
            'overall_summary' => [
                'total_progress_records' => $totalContents,
                'total_completed' => $totalCompleted,
                'total_in_progress' => $totalContents - $totalCompleted,
                'overall_completion_rate' => $totalContents > 0 ? round($totalCompleted / $totalContents * 100, 2) : 0,
                'average_progress' => round($progress->avg('progress_percentage') ?? 0, 2),
                'total_watch_time_seconds' => $progress->sum('watch_time'),
                'total_watch_time_formatted' => $this->formatWatchTime($progress->sum('watch_time')),
            ],
            'students' => $studentsReport,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getStudentContentProgressReport(int $studentId, array $filters): array
    {
        $query = ContentProgress::query()
            ->where('user_id', $studentId)
            ->with(['lessonContent.lesson.course', 'group']);

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('lessonContent.lesson', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('lessonContent', function ($q) use ($filters) {
                $q->where('lesson_id', $filters['lesson_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $progress = $query->orderBy('updated_at', 'desc')->get();

        $student = User::find($studentId);

        $total = $progress->count();
        $completed = $progress->where('is_completed', true)->count();
        $avgProgress = $progress->avg('progress_percentage');
        $totalWatchTime = $progress->sum('watch_time');

        return [
            'student' => [
                'id' => $student?->id,
                'name' => $student?->name,
                'email' => $student?->email,
            ],
            'summary' => [
                'total_contents' => $total,
                'completed_contents' => $completed,
                'in_progress_contents' => $total - $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($avgProgress ?? 0, 2),
                'total_watch_time_seconds' => $totalWatchTime,
                'total_watch_time_formatted' => $this->formatWatchTime($totalWatchTime),
            ],
            'by_course' => $this->groupByCourse($progress),
            'by_lesson' => $this->groupByLesson($progress),
            'contents' => $progress->map(fn($p) => [
                'id' => $p->id,
                'lesson_content_id' => $p->lesson_content_id,
                'content_title' => $p->lessonContent?->title,
                'lesson_title' => $p->lessonContent?->lesson?->title,
                'course_title' => $p->lessonContent?->lesson?->course?->title,
                'group_id' => $p->group_id,
                'progress_percentage' => $p->progress_percentage,
                'is_completed' => $p->is_completed,
                'watch_time_seconds' => $p->watch_time,
                'watch_time_formatted' => $this->formatWatchTime($p->watch_time),
                'last_position' => $p->last_position,
                'completed_at' => $p->completed_at?->toDateTimeString(),
                'updated_at' => $p->updated_at?->toDateTimeString(),
            ])->values(),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getLessonContentProgressReport(int $lessonId, array $filters): array
    {
        $lessonContents = LessonContent::where('lesson_id', $lessonId)
            ->where('contentable_type', 'App\\Features\\Courses\\Models\\VideoContent')
            ->with(['lesson.course'])
            ->get();

        $contentIds = $lessonContents->pluck('id');

        $query = ContentProgress::query()
            ->whereIn('lesson_content_id', $contentIds)
            ->with(['user', 'lessonContent']);

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // Filter by student
        if (!empty($filters['student_id'])) {
            $query->where('user_id', $filters['student_id']);
        }

        $this->applyDateFilters($query, $filters);

        $progress = $query->get();

        $contents = $lessonContents->map(function ($content) use ($progress) {
            $contentProgress = $progress->where('lesson_content_id', $content->id);
            $total = $contentProgress->count();
            $completed = $contentProgress->where('is_completed', true)->count();

            return [
                'content_id' => $content->id,
                'title' => $content->title,
                'total_students' => $total,
                'completed_count' => $completed,
                'in_progress_count' => $total - $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($contentProgress->avg('progress_percentage') ?? 0, 2),
                'total_watch_time' => $contentProgress->sum('watch_time'),
            ];
        });

        $lesson = $lessonContents->first()?->lesson;

        return [
            'lesson' => [
                'id' => $lesson?->id,
                'title' => $lesson?->title,
                'course' => $lesson?->course?->title,
            ],
            'contents' => $contents,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getGroupContentProgressReport(int $groupId, array $filters): array
    {
        $query = ContentProgress::query()
            ->where('group_id', $groupId)
            ->with(['user', 'lessonContent.lesson.course']);

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('lessonContent.lesson', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('lessonContent', function ($q) use ($filters) {
                $q->where('lesson_id', $filters['lesson_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $progress = $query->get();

        // Group by student
        $byStudent = $progress->groupBy('user_id');

        $studentsReport = $byStudent->map(function ($studentProgress, $userId) {
            $student = $studentProgress->first()->user;
            $total = $studentProgress->count();
            $completed = $studentProgress->where('is_completed', true)->count();

            return [
                'student_id' => $userId,
                'student_name' => $student?->name,
                'total_contents' => $total,
                'completed_contents' => $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($studentProgress->avg('progress_percentage') ?? 0, 2),
                'total_watch_time_seconds' => $studentProgress->sum('watch_time'),
                'total_watch_time_formatted' => $this->formatWatchTime($studentProgress->sum('watch_time')),
            ];
        })->sortByDesc('completion_rate')->values();

        $group = \App\Features\Groups\Models\Group::find($groupId);

        return [
            'group' => [
                'id' => $group?->id,
                'name' => $group?->name,
            ],
            'summary' => [
                'total_students' => $studentsReport->count(),
                'total_progress_records' => $progress->count(),
                'total_completed' => $progress->where('is_completed', true)->count(),
                'overall_completion_rate' => $progress->count() > 0
                    ? round($progress->where('is_completed', true)->count() / $progress->count() * 100, 2)
                    : 0,
                'average_progress' => round($progress->avg('progress_percentage') ?? 0, 2),
            ],
            'students' => $studentsReport,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function groupByCourse(Collection $progress): array
    {
        $byCourse = $progress->groupBy(fn($p) => $p->lessonContent?->lesson?->course_id);

        return $byCourse->map(function ($courseProgress, $courseId) {
            $course = $courseProgress->first()->lessonContent?->lesson?->course;
            $total = $courseProgress->count();
            $completed = $courseProgress->where('is_completed', true)->count();

            return [
                'course_id' => $courseId,
                'course_title' => $course?->title,
                'total_contents' => $total,
                'completed_contents' => $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($courseProgress->avg('progress_percentage') ?? 0, 2),
            ];
        })->filter(fn($item) => $item['course_id'] !== null)->values()->toArray();
    }

    protected function groupByLesson(Collection $progress): array
    {
        $byLesson = $progress->groupBy(fn($p) => $p->lessonContent?->lesson_id);

        return $byLesson->map(function ($lessonProgress, $lessonId) {
            $lesson = $lessonProgress->first()->lessonContent?->lesson;
            $total = $lessonProgress->count();
            $completed = $lessonProgress->where('is_completed', true)->count();

            return [
                'lesson_id' => $lessonId,
                'lesson_title' => $lesson?->title,
                'total_contents' => $total,
                'completed_contents' => $completed,
                'completion_rate' => $total > 0 ? round($completed / $total * 100, 2) : 0,
                'average_progress' => round($lessonProgress->avg('progress_percentage') ?? 0, 2),
            ];
        })->filter(fn($item) => $item['lesson_id'] !== null)->values()->toArray();
    }

    protected function applyDateFilters($query, array $filters): void
    {
        if (!empty($filters['period']) && $filters['period'] === 'this_week') {
            $query->whereBetween('updated_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        }

        if (!empty($filters['period']) && $filters['period'] === 'this_month') {
            $query->whereBetween('updated_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        if (!empty($filters['date_from'])) {
            $query->where('updated_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('updated_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
    }

    protected function formatWatchTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }
        return sprintf('%ds', $secs);
    }

    protected function getAppliedFilters(array $filters): array
    {
        return array_filter([
            'student_id' => $filters['student_id'] ?? null,
            'group_id' => $filters['group_id'] ?? null,
            'course_id' => $filters['course_id'] ?? null,
            'lesson_id' => $filters['lesson_id'] ?? null,
            'period' => $filters['period'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ]);
    }
}
