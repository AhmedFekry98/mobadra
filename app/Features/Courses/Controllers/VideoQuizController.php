<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\VideoQuiz;
use App\Features\Courses\Requests\VideoQuizRequest;
use App\Features\Courses\Requests\VideoQuizQuestionRequest;
use App\Features\Courses\Requests\VideoQuizAnswerRequest;
use App\Features\Courses\Services\VideoQuizService;
use App\Features\Courses\Transformers\VideoQuizResource;
use App\Features\Courses\Transformers\VideoQuizQuestionResource;
use App\Features\Courses\Transformers\VideoQuizAttemptResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class VideoQuizController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected VideoQuizService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function show(string $videoContentId)
    {
        return $this->executeService(function () use ($videoContentId) {
            $quiz = $this->service->getQuizByVideoId($videoContentId);

            if (!$quiz) {
                return $this->okResponse(null, "No quiz found for this video");
            }

            return $this->okResponse(
                VideoQuizResource::make($quiz),
                "Video quiz retrieved successfully"
            );
        }, 'VideoQuizController@show');
    }

    public function store(VideoQuizRequest $request, string $videoContentId)
    {
        return $this->executeService(function () use ($request, $videoContentId) {
            $quiz = $this->service->createOrUpdateQuiz($videoContentId, $request->validated());

            return $this->okResponse(
                VideoQuizResource::make($quiz),
                "Video quiz created successfully"
            );
        }, 'VideoQuizController@store');
    }

    public function addQuestion(VideoQuizQuestionRequest $request, string $quizId)
    {
        return $this->executeService(function () use ($request, $quizId) {
            $question = $this->service->addQuestion($quizId, $request->validated());

            return $this->okResponse(
                VideoQuizQuestionResource::make($question),
                "Question added successfully"
            );
        }, 'VideoQuizController@addQuestion');
    }

    public function updateQuestion(VideoQuizQuestionRequest $request, string $questionId)
    {
        return $this->executeService(function () use ($request, $questionId) {
            $question = $this->service->updateQuestion($questionId, $request->validated());

            return $this->okResponse(
                VideoQuizQuestionResource::make($question),
                "Question updated successfully"
            );
        }, 'VideoQuizController@updateQuestion');
    }

    public function deleteQuestion(string $questionId)
    {
        return $this->executeService(function () use ($questionId) {
            $this->service->deleteQuestion($questionId);

            return $this->okResponse(null, "Question deleted successfully");
        }, 'VideoQuizController@deleteQuestion');
    }

    public function startAttempt(string $quizId)
    {
        return $this->executeService(function () use ($quizId) {
            $studentId = auth()->user()->id;
            $attempt = $this->service->startAttempt($quizId, $studentId);

            return $this->okResponse(
                VideoQuizAttemptResource::make($attempt),
                "Quiz attempt started"
            );
        }, 'VideoQuizController@startAttempt');
    }

    public function submitAnswer(VideoQuizAnswerRequest $request, string $attemptId)
    {
        return $this->executeService(function () use ($request, $attemptId) {
            $data = $request->validated();
            $answer = $this->service->submitAnswer(
                $attemptId,
                $data['question_id'],
                $data['selected_option_id']
            );

            return $this->okResponse(
                [
                    'is_correct' => $answer->is_correct,
                    'points_earned' => $answer->points_earned,
                    'explanation' => $answer->question->explanation,
                ],
                "Answer submitted"
            );
        }, 'VideoQuizController@submitAnswer');
    }

    public function completeAttempt(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->completeAttempt($attemptId);

            return $this->okResponse(
                VideoQuizAttemptResource::make($attempt),
                "Quiz completed"
            );
        }, 'VideoQuizController@completeAttempt');
    }

    public function myAttempts(string $quizId)
    {
        return $this->executeService(function () use ($quizId) {
            $studentId = auth()->user()->id;
            $attempts = $this->service->getStudentAttempts($quizId, $studentId);

            return $this->okResponse(
                VideoQuizAttemptResource::collection($attempts),
                "Attempts retrieved"
            );
        }, 'VideoQuizController@myAttempts');
    }

    public function attemptResult(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->getAttemptResult($attemptId);

            return $this->okResponse(
                VideoQuizAttemptResource::make($attempt),
                "Attempt result retrieved"
            );
        }, 'VideoQuizController@attemptResult');
    }
}
