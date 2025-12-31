<?php

namespace App\Features\Reports\Services;

use App\Features\Courses\Models\Quiz;
use App\Features\Courses\Models\QuizAttempt;
use App\Features\Courses\Models\LessonContent;
use App\Features\SystemManagements\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class QuizReportService
{
    public function getQuizReport(array $filters): array
    {
        $query = QuizAttempt::query()
            ->with(['quiz', 'student', 'answers.question'])
            ->where('status', 'completed');

        // Filter by student
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // Filter by quiz
        if (!empty($filters['quiz_id'])) {
            $query->where('quiz_id', $filters['quiz_id']);
        }

        // Filter by course (through lesson content)
        if (!empty($filters['course_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent.lesson', function ($lq) use ($filters) {
                    $lq->where('course_id', $filters['course_id']);
                });
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent', function ($lc) use ($filters) {
                    $lc->where('lesson_id', $filters['lesson_id']);
                });
            });
        }

        $this->applyDateFilters($query, $filters);

        $attempts = $query->orderBy('completed_at', 'desc')->get();

        return $this->formatQuizReport($attempts, $filters);
    }

    public function getStudentQuizReport(int $studentId, array $filters): array
    {
        $query = QuizAttempt::query()
            ->where('student_id', $studentId)
            ->where('status', 'completed')
            ->with(['quiz.lessonContent.lesson', 'answers.question']);

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent.lesson', function ($lq) use ($filters) {
                    $lq->where('course_id', $filters['course_id']);
                });
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent', function ($lc) use ($filters) {
                    $lc->where('lesson_id', $filters['lesson_id']);
                });
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
            'by_quiz' => $this->groupByQuiz($attempts),
            'attempts' => $attempts->map(fn($a) => [
                'id' => $a->id,
                'quiz_id' => $a->quiz_id,
                'quiz_title' => $a->quiz?->lessonContent?->title ?? 'Quiz',
                'lesson' => $a->quiz?->lessonContent?->lesson?->title,
                'attempt_number' => $a->attempt_number,
                'score' => $a->score,
                'total_points' => $a->total_points,
                'percentage' => $a->percentage,
                'passed' => $a->passed,
                'completed_at' => $a->completed_at?->toDateTimeString(),
            ])->values(),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getAllStudentsQuizReport(array $filters): array
    {
        $query = QuizAttempt::query()
            ->where('status', 'completed')
            ->with(['quiz.lessonContent.lesson', 'student']);

        // Filter by course
        if (!empty($filters['course_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent.lesson', function ($lq) use ($filters) {
                    $lq->where('course_id', $filters['course_id']);
                });
            });
        }

        // Filter by lesson
        if (!empty($filters['lesson_id'])) {
            $query->whereHas('quiz', function ($q) use ($filters) {
                $q->whereHas('lessonContent', function ($lc) use ($filters) {
                    $lc->where('lesson_id', $filters['lesson_id']);
                });
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

    public function getLessonQuizReport(int $lessonId, array $filters): array
    {
        $lessonContents = LessonContent::where('lesson_id', $lessonId)
            ->where('contentable_type', 'App\\Features\\Courses\\Models\\Quiz')
            ->with(['contentable.attempts.student', 'contentable.questions'])
            ->get();

        $quizzes = $lessonContents->map(function ($content) use ($filters) {
            $quiz = $content->contentable;
            if (!$quiz) return null;

            $attempts = $quiz->attempts()->where('status', 'completed');

            if (!empty($filters['student_id'])) {
                $attempts->where('student_id', $filters['student_id']);
            }

            $attempts = $attempts->get();

            return [
                'quiz_id' => $quiz->id,
                'title' => $content->title,
                'total_questions' => $quiz->questions->count(),
                'total_points' => $quiz->questions->sum('points'),
                'passing_score' => $quiz->passing_score,
                'attempts_count' => $attempts->count(),
                'passed_count' => $attempts->where('passed', true)->count(),
                'average_score' => round($attempts->avg('percentage') ?? 0, 2),
                'highest_score' => $attempts->max('percentage') ?? 0,
                'lowest_score' => $attempts->min('percentage') ?? 0,
            ];
        })->filter()->values();

        return [
            'lesson_id' => $lessonId,
            'quizzes' => $quizzes,
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

    protected function formatQuizReport(Collection $attempts, array $filters): array
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
            'by_quiz' => $this->groupByQuiz($attempts),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function groupByQuiz(Collection $attempts): array
    {
        $byQuiz = $attempts->groupBy('quiz_id');

        return $byQuiz->map(function ($quizAttempts, $quizId) {
            $quiz = $quizAttempts->first()->quiz;
            $total = $quizAttempts->count();
            $passed = $quizAttempts->where('passed', true)->count();

            return [
                'quiz_id' => $quizId,
                'total_attempts' => $total,
                'passed' => $passed,
                'failed' => $total - $passed,
                'pass_rate' => $total > 0 ? round($passed / $total * 100, 2) : 0,
                'average_score' => round($quizAttempts->avg('percentage') ?? 0, 2),
            ];
        })->values()->toArray();
    }

    protected function getAppliedFilters(array $filters): array
    {
        return array_filter([
            'student_id' => $filters['student_id'] ?? null,
            'quiz_id' => $filters['quiz_id'] ?? null,
            'course_id' => $filters['course_id'] ?? null,
            'lesson_id' => $filters['lesson_id'] ?? null,
            'period' => $filters['period'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ]);
    }
}
