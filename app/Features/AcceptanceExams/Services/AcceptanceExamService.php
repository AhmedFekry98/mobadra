<?php

namespace App\Features\AcceptanceExams\Services;

use App\Features\AcceptanceExams\Models\AcceptanceExam;
use App\Features\AcceptanceExams\Models\AcceptanceExamAttempt;
use App\Features\AcceptanceExams\Models\AcceptanceExamAnswer;
use App\Features\AcceptanceExams\Models\AcceptanceExamQuestion;
use App\Features\AcceptanceExams\Models\AcceptanceExamQuestionOption;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Models\UserInformation;
use Illuminate\Support\Facades\DB;

class AcceptanceExamService
{
    public function getAllExams(?string $search = null, ?bool $paginate = false)
    {
        $query = AcceptanceExam::with(['questions.options']);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $paginate ? $query->paginate(15) : $query->get();
    }

    public function getExamById(int $id): AcceptanceExam
    {
        return AcceptanceExam::with(['questions.options'])->findOrFail($id);
    }

    public function createExam(array $data): AcceptanceExam
    {
        return AcceptanceExam::create($data);
    }

    public function updateExam(int $id, array $data): AcceptanceExam
    {
        $exam = AcceptanceExam::findOrFail($id);
        $exam->update($data);
        return $exam->fresh(['questions.options']);
    }

    public function deleteExam(int $id): bool
    {
        return AcceptanceExam::findOrFail($id)->delete();
    }

    public function createQuestion(int $examId, array $data): AcceptanceExamQuestion
    {
        return DB::transaction(function () use ($examId, $data) {
            $question = AcceptanceExamQuestion::create([
                'acceptance_exam_id' => $examId,
                'question' => $data['question'],
                'type' => $data['type'] ?? 'single_choice',
                'points' => $data['points'] ?? 1,
                'order' => $data['order'] ?? 0,
                'explanation' => $data['explanation'] ?? null,
            ]);

            if (!empty($data['options'])) {
                foreach ($data['options'] as $index => $option) {
                    AcceptanceExamQuestionOption::create([
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

    public function updateQuestion(int $questionId, array $data): AcceptanceExamQuestion
    {
        return DB::transaction(function () use ($questionId, $data) {
            $question = AcceptanceExamQuestion::findOrFail($questionId);

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
                    AcceptanceExamQuestionOption::create([
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
        return AcceptanceExamQuestion::findOrFail($questionId)->delete();
    }

    public function startAttempt(int $examId, int $studentId): AcceptanceExamAttempt
    {
        $exam = AcceptanceExam::findOrFail($examId);

        // Check if student already has any attempt (only 1 attempt allowed)
        $existingAttempt = AcceptanceExamAttempt::where('acceptance_exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingAttempt) {
            throw new \Exception('You have already taken this acceptance exam');
        }

        return AcceptanceExamAttempt::create([
            'acceptance_exam_id' => $examId,
            'student_id' => $studentId,
            'attempt_number' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function submitAnswer(int $attemptId, int $questionId, array $data): AcceptanceExamAnswer
    {
        $attempt = AcceptanceExamAttempt::findOrFail($attemptId);

        if ($attempt->status !== 'in_progress') {
            throw new \Exception('This attempt has already been completed');
        }

        $answer = AcceptanceExamAnswer::updateOrCreate(
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

    public function completeAttempt(int $attemptId): AcceptanceExamAttempt
    {
        try {
            DB::beginTransaction();
            $attempt = AcceptanceExamAttempt::with(['answers.question', 'acceptanceExam', 'student.userInformation'])->findOrFail($attemptId);


        if ($attempt->status !== 'in_progress') {
            throw new \Exception('This attempt has already been completed');
        }

        $attempt->status = 'completed';
        $attempt->completed_at = now();
        $attempt->save();

        $attempt->calculateScore();

        // Update user_information.acceptance_exam to 'waiting'
        $userInfo = $attempt->student->userInformation;
        if ($userInfo) {
            $userInfo->update(['acceptance_exam' => 'waiting']);
        } else {
            UserInformation::create([
                'user_id' => $attempt->student_id,
                'acceptance_exam' => 'waiting',
            ]);
        }
        DB::commit();

        return $attempt->fresh(['answers.question', 'acceptanceExam']);
        } catch (\Exception $e) {

            DB::rollBack();
            throw new \Exception('This attempt has already been completed');
        }
    }

    public function getAttemptResult(int $attemptId): AcceptanceExamAttempt
    {
        return AcceptanceExamAttempt::with([
            'answers.question.options',
            'answers.selectedOption',
            'acceptanceExam',
            'student'
        ])->findOrFail($attemptId);
    }

    public function getAttemptsByExamId(int $examId)
    {
        return AcceptanceExamAttempt::with(['student'])
            ->where('acceptance_exam_id', $examId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStudentAttempts(int $studentId)
    {
        return AcceptanceExamAttempt::with(['acceptanceExam'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getStudentsByAcceptanceStatus(?string $status = null, ?string $search = null, ?bool $paginate = false)
    {
        $query = User::with(['userInformation', 'role'])
            ->whereHas('role', fn($q) => $q->where('name', 'student'))
            ->whereHas('userInformation');

        if ($status) {
            $query->whereHas('userInformation', fn($q) => $q->where('acceptance_exam', $status));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $paginate ? $query->paginate(15) : $query->get();
    }

    public function updateStudentAcceptanceStatus(int $userId, string $status): UserInformation
    {
        $validStatuses = ['pending', 'accepted', 'rejected', 'waiting'];
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid status. Must be one of: ' . implode(', ', $validStatuses));
        }

        $userInfo = UserInformation::where('user_id', $userId)->first();

        if (!$userInfo) {
            throw new \Exception('User information not found');
        }

        $userInfo->update(['acceptance_exam' => $status]);

        return $userInfo->fresh();
    }
}
