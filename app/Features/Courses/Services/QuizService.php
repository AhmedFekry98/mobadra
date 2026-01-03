<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Models\Quiz;
use App\Features\Courses\Models\QuizAttempt;
use App\Features\Courses\Models\QuizAnswer;
use App\Features\Courses\Models\QuizQuestion;
use App\Features\Courses\Models\QuizQuestionOption;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function getQuizById(int $id): Quiz
    {
        return Quiz::with(['questions.options', 'lessonContent.lesson.course.term'])->findOrFail($id);
    }

    public function createQuestion(int $quizId, array $data): QuizQuestion
    {
        return DB::transaction(function () use ($quizId, $data) {
            $question = QuizQuestion::create([
                'quiz_id' => $quizId,
                'question' => $data['question'],
                'type' => $data['type'] ?? 'single_choice',
                'points' => $data['points'] ?? 1,
                'order' => $data['order'] ?? 0,
                'explanation' => $data['explanation'] ?? null,
            ]);

            if (!empty($data['options'])) {
                foreach ($data['options'] as $index => $option) {
                    QuizQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            return $question->load('options');
        });
    }

    public function updateQuestion(int $questionId, array $data): QuizQuestion
    {
        return DB::transaction(function () use ($questionId, $data) {
            $question = QuizQuestion::findOrFail($questionId);

            $question->update([
                'question' => $data['question'] ?? $question->question,
                'type' => $data['type'] ?? $question->type,
                'points' => $data['points'] ?? $question->points,
                'order' => $data['order'] ?? $question->order,
                'explanation' => $data['explanation'] ?? $question->explanation,
            ]);

            if (isset($data['options'])) {
                $question->options()->delete();
                foreach ($data['options'] as $index => $option) {
                    QuizQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $index,
                    ]);
                }
            }

            return $question->load('options');
        });
    }

    public function deleteQuestion(int $questionId): bool
    {
        return QuizQuestion::findOrFail($questionId)->delete();
    }

    public function startAttempt(int $quizId, int $studentId): QuizAttempt
    {
        $quiz = Quiz::findOrFail($quizId);

        if (!$quiz->canStudentAttempt($studentId)) {
            throw new \Exception('Maximum attempts reached for this quiz');
        }

        $attemptNumber = $quiz->attempts()
            ->where('student_id', $studentId)
            ->count() + 1;

        return QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $studentId,
            'attempt_number' => $attemptNumber,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function submitAnswer(int $attemptId, int $questionId, array $data): QuizAnswer
    {
        $attempt = QuizAttempt::findOrFail($attemptId);

        if ($attempt->status !== 'in_progress') {
            throw new \Exception('This attempt has already been completed');
        }

        $answer = QuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $data['selected_option_id'] ?? null,
                'text_answer' => $data['text_answer'] ?? null,
            ]
        );

        $answer->checkAnswer();

        return $answer;
    }

    public function completeAttempt(int $attemptId): QuizAttempt
    {
        $attempt = QuizAttempt::with(['answers.question', 'quiz'])->findOrFail($attemptId);

        if ($attempt->status !== 'in_progress') {
            throw new \Exception('This attempt has already been completed');
        }

        $attempt->status = 'completed';
        $attempt->completed_at = now();
        $attempt->save();

        $attempt->calculateScore();

        return $attempt->fresh(['answers.question', 'quiz']);
    }

    public function getAttemptResult(int $attemptId): QuizAttempt
    {
        return QuizAttempt::with([
            'answers.question.options',
            'answers.selectedOption',
            'quiz',
            'student'
        ])->findOrFail($attemptId);
    }

    public function getAttemptsByQuizId(int $quizId)
    {
        return QuizAttempt::with(['student'])
            ->where('quiz_id', $quizId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getQuizResults(int $quizId): array
    {
        $attempts = QuizAttempt::with(['student'])
            ->where('quiz_id', $quizId)
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get();

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('percentage'),
            'pass_rate' => $attempts->where('passed', true)->count() / max($attempts->count(), 1) * 100,
            'attempts' => $attempts,
        ];
    }

    // ==================== Final Quizzes (Multiple) ====================

    public function getCourseFinalQuizzes(int $courseId)
    {
        $course = Course::with('term')->findOrFail($courseId);
        return $course->finalQuizzes()->with(['questions.options'])->get();
    }

    public function createFinalQuiz(int $courseId, array $data): Quiz
    {
        return DB::transaction(function () use ($courseId, $data) {
            $course = Course::findOrFail($courseId);

            $quiz = Quiz::create([
                'time_limit' => $data['time_limit'] ?? null,
                'passing_score' => $data['passing_score'] ?? 60,
                'max_attempts' => $data['max_attempts'] ?? 1,
                'shuffle_questions' => $data['shuffle_questions'] ?? false,
                'show_answers' => $data['show_answers'] ?? false,
            ]);

            $maxOrder = $course->finalQuizzes()->max('course_final_quizzes.order') ?? 0;

            $course->finalQuizzes()->attach($quiz->id, [
                'title' => $data['title'] ?? null,
                'description' => $data['description'] ?? null,
                'order' => $data['order'] ?? $maxOrder + 1,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return $quiz->load('questions.options');
        });
    }

    public function getFinalQuizByCourseId(int $courseId, int $quizId): Quiz
    {
        $course = Course::findOrFail($courseId);

        $quiz = $course->finalQuizzes()
            ->where('quizzes.id', $quizId)
            ->with(['questions.options', 'lessonContent.lesson.course.term'])
            ->first();

        if (!$quiz) {
            throw new \Exception('Final quiz not found for this course');
        }

        return $quiz;
    }

    public function updateFinalQuiz(int $courseId, int $quizId, array $data): Quiz
    {
        return DB::transaction(function () use ($courseId, $quizId, $data) {
            $course = Course::findOrFail($courseId);

            $quiz = $course->finalQuizzes()->where('quizzes.id', $quizId)->first();

            if (!$quiz) {
                throw new \Exception('Final quiz not found for this course');
            }

            $quiz->update([
                'time_limit' => $data['time_limit'] ?? $quiz->time_limit,
                'passing_score' => $data['passing_score'] ?? $quiz->passing_score,
                'max_attempts' => $data['max_attempts'] ?? $quiz->max_attempts,
                'shuffle_questions' => $data['shuffle_questions'] ?? $quiz->shuffle_questions,
                'show_answers' => $data['show_answers'] ?? $quiz->show_answers,
            ]);

            $pivotData = [];
            if (isset($data['title'])) $pivotData['title'] = $data['title'];
            if (isset($data['description'])) $pivotData['description'] = $data['description'];
            if (isset($data['order'])) $pivotData['order'] = $data['order'];
            if (isset($data['is_active'])) $pivotData['is_active'] = $data['is_active'];

            if (!empty($pivotData)) {
                $course->finalQuizzes()->updateExistingPivot($quizId, $pivotData);
            }

            return $quiz->fresh(['questions.options']);
        });
    }

    public function deleteFinalQuiz(int $courseId, int $quizId): bool
    {
        return DB::transaction(function () use ($courseId, $quizId) {
            $course = Course::findOrFail($courseId);

            $quiz = $course->finalQuizzes()->where('quizzes.id', $quizId)->first();

            if (!$quiz) {
                throw new \Exception('Final quiz not found for this course');
            }

            $course->finalQuizzes()->detach($quizId);
            return $quiz->delete();
        });
    }

    public function submitFinalQuiz(int $courseId, int $quizId, int $studentId, array $answers): QuizAttempt
    {
        return DB::transaction(function () use ($courseId, $quizId, $studentId, $answers) {
            $course = Course::findOrFail($courseId);

            $quiz = $course->finalQuizzes()->where('quizzes.id', $quizId)->first();

            if (!$quiz) {
                throw new \Exception('Final quiz not found for this course');
            }

            if (!$quiz->pivot->is_active) {
                throw new \Exception('This final quiz is not active');
            }

            if (!$quiz->canStudentAttempt($studentId)) {
                throw new \Exception('Maximum attempts reached for this quiz');
            }

            $attemptNumber = $quiz->attempts()
                ->where('student_id', $studentId)
                ->count() + 1;

            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $studentId,
                'attempt_number' => $attemptNumber,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            foreach ($answers as $answer) {
                $quizAnswer = QuizAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $answer['question_id'],
                    'selected_option_id' => $answer['selected_option_id'] ?? null,
                    'text_answer' => $answer['text_answer'] ?? null,
                ]);
                $quizAnswer->checkAnswer();
            }

            $attempt->status = 'completed';
            $attempt->completed_at = now();
            $attempt->save();

            $attempt->calculateScore();

            return $attempt->fresh(['answers.question', 'quiz']);
        });
    }
}
