<?php

namespace App\Features\Courses\Services;

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
        return Quiz::with(['questions.options'])->findOrFail($id);
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
}
