<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Models\VideoContent;
use App\Features\Courses\Models\VideoQuiz;
use App\Features\Courses\Models\VideoQuizAnswer;
use App\Features\Courses\Models\VideoQuizAttempt;
use App\Features\Courses\Models\VideoQuizQuestion;
use Illuminate\Support\Facades\DB;

class VideoQuizService
{
    public function getQuizByVideoId(int $videoContentId): ?VideoQuiz
    {
        return VideoQuiz::where('video_content_id', $videoContentId)
            ->with(['questions.options'])
            ->first();
    }

    public function createOrUpdateQuiz(int $videoContentId, array $data): VideoQuiz
    {
        $videoContent = VideoContent::findOrFail($videoContentId);

        $quiz = VideoQuiz::updateOrCreate(
            ['video_content_id' => $videoContentId],
            [
                'max_questions' => $data['max_questions'] ?? 3,
                'passing_score' => $data['passing_score'] ?? 60,
                'is_required' => $data['is_required'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        return $quiz->load('questions.options');
    }

    public function addQuestion(int $quizId, array $data): VideoQuizQuestion
    {
        $quiz = VideoQuiz::findOrFail($quizId);

        if ($quiz->questions()->count() >= $quiz->max_questions) {
            throw new \Exception("Maximum number of questions ({$quiz->max_questions}) reached for this video quiz");
        }

        return DB::transaction(function () use ($quizId, $data) {
            $maxOrder = VideoQuizQuestion::where('video_quiz_id', $quizId)->max('order') ?? 0;

            $question = VideoQuizQuestion::create([
                'video_quiz_id' => $quizId,
                'question' => $data['question'],
                'type' => $data['type'] ?? 'single_choice',
                'points' => $data['points'] ?? 1,
                'order' => $data['order'] ?? $maxOrder + 1,
                'timestamp_seconds' => $data['timestamp_seconds'] ?? null,
                'explanation' => $data['explanation'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['options'])) {
                foreach ($data['options'] as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $option['order'] ?? $index + 1,
                    ]);
                }
            }

            return $question->load('options');
        });
    }

    public function updateQuestion(int $questionId, array $data): VideoQuizQuestion
    {
        return DB::transaction(function () use ($questionId, $data) {
            $question = VideoQuizQuestion::findOrFail($questionId);

            $question->update([
                'question' => $data['question'] ?? $question->question,
                'type' => $data['type'] ?? $question->type,
                'points' => $data['points'] ?? $question->points,
                'order' => $data['order'] ?? $question->order,
                'timestamp_seconds' => $data['timestamp_seconds'] ?? $question->timestamp_seconds,
                'explanation' => $data['explanation'] ?? $question->explanation,
                'is_active' => $data['is_active'] ?? $question->is_active,
            ]);

            if (isset($data['options'])) {
                $question->options()->delete();
                foreach ($data['options'] as $index => $option) {
                    $question->options()->create([
                        'option_text' => $option['option_text'],
                        'is_correct' => $option['is_correct'] ?? false,
                        'order' => $option['order'] ?? $index + 1,
                    ]);
                }
            }

            return $question->load('options');
        });
    }

    public function deleteQuestion(int $questionId): bool
    {
        $question = VideoQuizQuestion::findOrFail($questionId);
        return $question->delete();
    }

    public function startAttempt(int $quizId, int $studentId): VideoQuizAttempt
    {
        $quiz = VideoQuiz::findOrFail($quizId);

        $existingAttempt = VideoQuizAttempt::where('video_quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return $existingAttempt->load(['videoQuiz.questions.options', 'answers']);
        }

        $attempt = VideoQuizAttempt::create([
            'video_quiz_id' => $quizId,
            'student_id' => $studentId,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return $attempt->load(['videoQuiz.questions.options']);
    }

    public function submitAnswer(int $attemptId, int $questionId, int $selectedOptionId): VideoQuizAnswer
    {
        $attempt = VideoQuizAttempt::findOrFail($attemptId);

        if ($attempt->status === 'completed') {
            throw new \Exception('This attempt has already been completed');
        }

        $answer = VideoQuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attemptId,
                'question_id' => $questionId,
            ],
            [
                'selected_option_id' => $selectedOptionId,
            ]
        );

        $answer->checkAnswer();

        return $answer->load(['question', 'selectedOption']);
    }

    public function completeAttempt(int $attemptId): VideoQuizAttempt
    {
        $attempt = VideoQuizAttempt::findOrFail($attemptId);

        if ($attempt->status === 'completed') {
            throw new \Exception('This attempt has already been completed');
        }

        $attempt->calculateScore();

        return $attempt->load(['videoQuiz', 'answers.question', 'answers.selectedOption']);
    }

    public function getStudentAttempts(int $quizId, int $studentId)
    {
        return VideoQuizAttempt::where('video_quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->with(['answers.question', 'answers.selectedOption'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAttemptResult(int $attemptId): VideoQuizAttempt
    {
        return VideoQuizAttempt::with([
            'videoQuiz.questions.options',
            'answers.question.options',
            'answers.selectedOption',
            'student'
        ])->findOrFail($attemptId);
    }
}
