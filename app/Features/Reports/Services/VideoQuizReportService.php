<?php

namespace App\Features\Reports\Services;

use App\Features\Courses\Models\VideoQuiz;
use App\Features\Courses\Models\VideoQuizAttempt;
use App\Features\Courses\Models\VideoContent;
use App\Features\SystemManagements\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VideoQuizReportService
{
    public function getVideoQuizReport(array $filters): array
    {
        $query = VideoQuizAttempt::query()
            ->with(['videoQuiz.videoContent', 'student', 'answers.question'])
            ->where('status', 'completed');

        // Filter by student
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // Filter by video quiz
        if (!empty($filters['video_quiz_id'])) {
            $query->where('video_quiz_id', $filters['video_quiz_id']);
        }

        // Filter by video content
        if (!empty($filters['video_content_id'])) {
            $query->whereHas('videoQuiz', function ($q) use ($filters) {
                $q->where('video_content_id', $filters['video_content_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $attempts = $query->orderBy('completed_at', 'desc')->get();

        return $this->formatVideoQuizReport($attempts, $filters);
    }

    public function getStudentVideoQuizReport(int $studentId, array $filters): array
    {
        $query = VideoQuizAttempt::query()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->with(['videoQuiz.videoContent.lessonContent.lesson', 'answers.question']);

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('videoQuiz.videoContent.lessonContent', function ($q) use ($filters) {
                $q->where('lesson_id', $filters['lesson_id']);
            });
        }

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('videoQuiz.videoContent.lessonContent.lesson', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $attempts = $query->orderBy('completed_at', 'desc')->get();

        $student = User::find($studentId);

        $totalAttempts = $attempts->count();
        $passedCount = $attempts->where('passed', true)->count();
        $averageScore = $attempts->avg('percentage') ?? 0;

        return [
            'student' => [
                'id' => $student?->id,
                'name' => $student?->name,
                'email' => $student?->email,
            ],
            'summary' => [
                'total_attempts' => $totalAttempts,
                'passed' => $passedCount,
                'failed' => $totalAttempts - $passedCount,
                'pass_rate' => $totalAttempts > 0 ? round($passedCount / $totalAttempts * 100, 2) : 0,
                'average_score' => round($averageScore, 2),
                'total_points_earned' => $attempts->sum('score'),
                'total_points_possible' => $attempts->sum('total_points'),
            ],
            'by_video' => $this->groupByVideo($attempts),
            'attempts' => $attempts->map(fn($a) => [
                'id' => $a->id,
                'video_quiz_id' => $a->video_quiz_id,
                'video_title' => $a->videoQuiz?->videoContent?->lessonContent?->title ?? 'Video',
                'lesson' => $a->videoQuiz?->videoContent?->lessonContent?->lesson?->title,
                'score' => $a->score,
                'total_points' => $a->total_points,
                'percentage' => $a->percentage,
                'passed' => $a->passed,
                'completed_at' => $a->completed_at?->toDateTimeString(),
            ])->values(),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getAllStudentsVideoQuizReport(array $filters): array
    {
        $query = VideoQuizAttempt::query()
            ->where('status', 'completed')
            ->with(['videoQuiz.videoContent.lessonContent.lesson', 'student']);

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('videoQuiz.videoContent.lessonContent', function ($q) use ($filters) {
                $q->where('lesson_id', $filters['lesson_id']);
            });
        }

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('videoQuiz.videoContent.lessonContent.lesson', function ($q) use ($filters) {
                $q->where('course_id', $filters['course_id']);
            });
        }

        $this->applyDateFilters($query, $filters);

        $attempts = $query->get();

        // Group by student
        $byStudent = $attempts->groupBy('student_id');

        $studentsReport = $byStudent->map(function ($studentAttempts, $studentId) {
            $student = $studentAttempts->first()->student;
            $total = $studentAttempts->count();
            $passed = $studentAttempts->where('passed', true)->count();

            return [
                'student_id' => $studentId,
                'student_name' => $student?->name,
                'total_attempts' => $total,
                'passed' => $passed,
                'failed' => $total - $passed,
                'pass_rate' => $total > 0 ? round($passed / $total * 100, 2) : 0,
                'average_score' => round($studentAttempts->avg('percentage') ?? 0, 2),
                'total_points_earned' => $studentAttempts->sum('score'),
            ];
        })->sortByDesc('average_score')->values();

        return [
            'total_students' => $studentsReport->count(),
            'overall_summary' => [
                'total_attempts' => $attempts->count(),
                'passed' => $attempts->where('passed', true)->count(),
                'failed' => $attempts->where('passed', false)->count(),
                'average_score' => round($attempts->avg('percentage') ?? 0, 2),
            ],
            'students' => $studentsReport,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getVideoQuizzesByLesson(int $lessonId, array $filters): array
    {
        $videoQuizzes = VideoQuiz::whereHas('videoContent.lessonContent', function ($q) use ($lessonId) {
            $q->where('lesson_id', $lessonId);
        })->with(['videoContent.lessonContent', 'questions', 'attempts' => function ($q) use ($filters) {
            $q->where('status', 'completed');
            if (!empty($filters['student_id'])) {
                $q->where('student_id', $filters['student_id']);
            }
        }])->get();

        $quizzes = $videoQuizzes->map(function ($quiz) {
            $attempts = $quiz->attempts;

            return [
                'video_quiz_id' => $quiz->id,
                'video_title' => $quiz->videoContent?->lessonContent?->title ?? 'Video',
                'total_questions' => $quiz->questions->count(),
                'max_questions' => $quiz->max_questions,
                'passing_score' => $quiz->passing_score,
                'attempts_count' => $attempts->count(),
                'passed_count' => $attempts->where('passed', true)->count(),
                'average_score' => round($attempts->avg('percentage') ?? 0, 2),
                'highest_score' => $attempts->max('percentage') ?? 0,
                'lowest_score' => $attempts->min('percentage') ?? 0,
            ];
        });

        return [
            'lesson_id' => $lessonId,
            'video_quizzes' => $quizzes,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function applyDateFilters($query, array $filters): void
    {
        if (!empty($filters['period']) && $filters['period'] === 'this_week') {
            $query->whereBetween('completed_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        }

        if (!empty($filters['period']) && $filters['period'] === 'this_month') {
            $query->whereBetween('completed_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        if (!empty($filters['date_from'])) {
            $query->where('completed_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('completed_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
    }

    protected function formatVideoQuizReport(Collection $attempts, array $filters): array
    {
        $totalAttempts = $attempts->count();
        $passedCount = $attempts->where('passed', true)->count();

        return [
            'summary' => [
                'total_attempts' => $totalAttempts,
                'passed' => $passedCount,
                'failed' => $totalAttempts - $passedCount,
                'pass_rate' => $totalAttempts > 0 ? round($passedCount / $totalAttempts * 100, 2) : 0,
                'average_score' => round($attempts->avg('percentage') ?? 0, 2),
            ],
            'by_video' => $this->groupByVideo($attempts),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function groupByVideo(Collection $attempts): array
    {
        $byVideo = $attempts->groupBy('video_quiz_id');

        return $byVideo->map(function ($videoAttempts, $videoQuizId) {
            $videoQuiz = $videoAttempts->first()->videoQuiz;
            $total = $videoAttempts->count();
            $passed = $videoAttempts->where('passed', true)->count();

            return [
                'video_quiz_id' => $videoQuizId,
                'video_title' => $videoQuiz?->videoContent?->lessonContent?->title ?? 'Video',
                'total_attempts' => $total,
                'passed' => $passed,
                'failed' => $total - $passed,
                'pass_rate' => $total > 0 ? round($passed / $total * 100, 2) : 0,
                'average_score' => round($videoAttempts->avg('percentage') ?? 0, 2),
            ];
        })->values()->toArray();
    }

    protected function getAppliedFilters(array $filters): array
    {
        return array_filter([
            'student_id' => $filters['student_id'] ?? null,
            'video_quiz_id' => $filters['video_quiz_id'] ?? null,
            'video_content_id' => $filters['video_content_id'] ?? null,
            'lesson_id' => $filters['lesson_id'] ?? null,
            'course_id' => $filters['course_id'] ?? null,
            'period' => $filters['period'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ]);
    }
}
