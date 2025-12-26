<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Requests\CreateQuestionRequest;
use App\Features\Courses\Requests\FinalQuizRequest;
use App\Features\Courses\Requests\SubmitAnswerRequest;
use App\Features\Courses\Services\QuizService;
use App\Features\Courses\Transformers\QuizResource;
use App\Features\Courses\Transformers\QuizAttemptResource;
use App\Features\Courses\Transformers\QuizQuestionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class QuizController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected QuizService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $quiz = $this->service->getQuizById($id);

            return $this->okResponse(
                QuizResource::make($quiz),
                "Quiz retrieved successfully"
            );
        }, 'QuizController@show');
    }

    public function storeQuestion(string $quizId, CreateQuestionRequest $request)
    {
        return $this->executeService(function () use ($quizId, $request) {
            $question = $this->service->createQuestion($quizId, $request->validated());

            return $this->okResponse(
                QuizQuestionResource::make($question),
                "Question created successfully"
            );
        }, 'QuizController@storeQuestion');
    }

    public function updateQuestion(string $questionId, CreateQuestionRequest $request)
    {
        return $this->executeService(function () use ($questionId, $request) {
            $question = $this->service->updateQuestion($questionId, $request->validated());

            return $this->okResponse(
                QuizQuestionResource::make($question),
                "Question updated successfully"
            );
        }, 'QuizController@updateQuestion');
    }

    public function destroyQuestion(string $questionId)
    {
        return $this->executeService(function () use ($questionId) {
            $this->service->deleteQuestion($questionId);

            return $this->okResponse(null, "Question deleted successfully");
        }, 'QuizController@destroyQuestion');
    }

    public function startAttempt(string $quizId)
    {
        return $this->executeService(function () use ($quizId) {
            $studentId = auth()->user()->id;
            $attempt = $this->service->startAttempt($quizId, $studentId);

            return $this->okResponse(
                QuizAttemptResource::make($attempt),
                "Quiz attempt started"
            );
        }, 'QuizController@startAttempt');
    }

    public function submitAnswer(string $attemptId, string $questionId, SubmitAnswerRequest $request)
    {
        return $this->executeService(function () use ($attemptId, $questionId, $request) {
            $answer = $this->service->submitAnswer($attemptId, $questionId, $request->validated());

            return $this->okResponse(
                $answer,
                "Answer submitted"
            );
        }, 'QuizController@submitAnswer');
    }

    public function completeAttempt(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->completeAttempt($attemptId);

            return $this->okResponse(
                QuizAttemptResource::make($attempt),
                "Quiz completed"
            );
        }, 'QuizController@completeAttempt');
    }

    public function attemptResult(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->getAttemptResult($attemptId);

            return $this->okResponse(
                QuizAttemptResource::make($attempt),
                "Attempt result retrieved"
            );
        }, 'QuizController@attemptResult');
    }

    public function quizResults(string $quizId)
    {
        return $this->executeService(function () use ($quizId) {
            $results = $this->service->getQuizResults($quizId);

            return $this->okResponse(
                $results,
                "Quiz results retrieved"
            );
        }, 'QuizController@quizResults');
    }

    public function storeFinalQuiz(string $courseId, FinalQuizRequest $request)
    {
        return $this->executeService(function () use ($courseId, $request) {
            $quiz = $this->service->createFinalQuiz($courseId, $request->validated());

            return $this->okResponse(
                QuizResource::make($quiz),
                "Final Quiz created successfully"
            );
        }, 'QuizController@storeFinalQuiz');
    }

    public function showFinalQuiz(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $quiz = $this->service->getFinalQuizByCourseId($courseId);

            if (!$quiz) {
                return $this->okResponse(
                    null,
                    "This course does not have a final quiz"
                );
            }

            return $this->okResponse(
                QuizResource::make($quiz),
                "Final Quiz retrieved successfully"
            );
        }, 'QuizController@showFinalQuiz');
    }

    public function updateFinalQuiz(string $courseId, FinalQuizRequest $request)
    {
        return $this->executeService(function () use ($courseId, $request) {
            $quiz = $this->service->updateFinalQuiz($courseId, $request->validated());

            return $this->okResponse(
                QuizResource::make($quiz),
                "Final Quiz updated successfully"
            );
        }, 'QuizController@updateFinalQuiz');
    }

    public function destroyFinalQuiz(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $this->service->deleteFinalQuiz($courseId);

            return $this->okResponse(
                null,
                "Final Quiz deleted successfully"
            );
        }, 'QuizController@destroyFinalQuiz');
    }
}
