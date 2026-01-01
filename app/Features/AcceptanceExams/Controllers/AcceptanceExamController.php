<?php

namespace App\Features\AcceptanceExams\Controllers;

use App\Features\AcceptanceExams\Models\AcceptanceExam;
use App\Features\AcceptanceExams\Requests\AcceptanceExamRequest;
use App\Features\AcceptanceExams\Requests\AcceptanceExamQuestionRequest;
use App\Features\AcceptanceExams\Requests\AcceptanceExamAnswerRequest;
use App\Features\AcceptanceExams\Services\AcceptanceExamService;
use App\Features\AcceptanceExams\Transformers\AcceptanceExamResource;
use App\Features\AcceptanceExams\Transformers\AcceptanceExamCollection;
use App\Features\AcceptanceExams\Transformers\AcceptanceExamQuestionResource;
use App\Features\AcceptanceExams\Transformers\AcceptanceExamAttemptResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AcceptanceExamController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected AcceptanceExamService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $result = $this->service->getAllExams(
                search: $request->get('search'),
                paginate: $request->has('page')
            );

            return $this->okResponse(
                $request->has('page')
                    ? AcceptanceExamCollection::make($result)
                    : AcceptanceExamResource::collection($result),
                "Acceptance exams retrieved successfully"
            );
        }, 'AcceptanceExamController@index');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $exam = $this->service->getExamById($id);

            return $this->okResponse(
                AcceptanceExamResource::make($exam),
                "Acceptance exam retrieved successfully"
            );
        }, 'AcceptanceExamController@show');
    }

    public function store(AcceptanceExamRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $exam = $this->service->createExam($request->validated());

            return $this->okResponse(
                AcceptanceExamResource::make($exam),
                "Acceptance exam created successfully"
            );
        }, 'AcceptanceExamController@store');
    }

    public function update(AcceptanceExamRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $exam = $this->service->updateExam($id, $request->validated());

            return $this->okResponse(
                AcceptanceExamResource::make($exam),
                "Acceptance exam updated successfully"
            );
        }, 'AcceptanceExamController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->service->deleteExam($id);

            return $this->okResponse(
                null,
                "Acceptance exam deleted successfully"
            );
        }, 'AcceptanceExamController@destroy');
    }

    public function storeQuestion(AcceptanceExamQuestionRequest $request, string $examId)
    {
        return $this->executeService(function () use ($request, $examId) {
            $question = $this->service->createQuestion($examId, $request->validated());

            return $this->okResponse(
                AcceptanceExamQuestionResource::make($question),
                "Question created successfully"
            );
        }, 'AcceptanceExamController@storeQuestion');
    }

    public function updateQuestion(AcceptanceExamQuestionRequest $request, string $questionId)
    {
        return $this->executeService(function () use ($request, $questionId) {
            $question = $this->service->updateQuestion($questionId, $request->validated());

            return $this->okResponse(
                AcceptanceExamQuestionResource::make($question),
                "Question updated successfully"
            );
        }, 'AcceptanceExamController@updateQuestion');
    }

    public function destroyQuestion(string $questionId)
    {
        return $this->executeService(function () use ($questionId) {
            $this->service->deleteQuestion($questionId);

            return $this->okResponse(
                null,
                "Question deleted successfully"
            );
        }, 'AcceptanceExamController@destroyQuestion');
    }

    public function startAttempt(string $examId)
    {
        return $this->executeService(function () use ($examId) {
            $attempt = $this->service->startAttempt($examId, auth()->id());

            return $this->okResponse(
                AcceptanceExamAttemptResource::make($attempt),
                "Exam attempt started successfully"
            );
        }, 'AcceptanceExamController@startAttempt');
    }

    public function submitAnswer(AcceptanceExamAnswerRequest $request, string $attemptId, string $questionId)
    {
        return $this->executeService(function () use ($request, $attemptId, $questionId) {
            $answer = $this->service->submitAnswer($attemptId, $questionId, $request->validated());

            return $this->okResponse(
                $answer,
                "Answer submitted successfully"
            );
        }, 'AcceptanceExamController@submitAnswer');
    }

    public function completeAttempt(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->completeAttempt($attemptId);

            return $this->okResponse(
                AcceptanceExamAttemptResource::make($attempt),
                "Exam completed successfully"
            );
        }, 'AcceptanceExamController@completeAttempt');
    }

    public function getAttemptResult(string $attemptId)
    {
        return $this->executeService(function () use ($attemptId) {
            $attempt = $this->service->getAttemptResult($attemptId);

            return $this->okResponse(
                AcceptanceExamAttemptResource::make($attempt),
                "Attempt result retrieved successfully"
            );
        }, 'AcceptanceExamController@getAttemptResult');
    }

    public function getExamAttempts(string $examId)
    {
        return $this->executeService(function () use ($examId) {
            $attempts = $this->service->getAttemptsByExamId($examId);

            return $this->okResponse(
                AcceptanceExamAttemptResource::collection($attempts),
                "Exam attempts retrieved successfully"
            );
        }, 'AcceptanceExamController@getExamAttempts');
    }

    public function getMyAttempts()
    {
        return $this->executeService(function () {
            $attempts = $this->service->getStudentAttempts(auth()->id());

            return $this->okResponse(
                AcceptanceExamAttemptResource::collection($attempts),
                "Your attempts retrieved successfully"
            );
        }, 'AcceptanceExamController@getMyAttempts');
    }

    public function getStudentsByAcceptanceStatus(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $students = $this->service->getStudentsByAcceptanceStatus(
                status: $request->get('status'),
                search: $request->get('search'),
                paginate: $request->has('page')
            );

            return $this->okResponse(
                $students,
                "Students retrieved successfully"
            );
        }, 'AcceptanceExamController@getStudentsByAcceptanceStatus');
    }

    public function updateStudentAcceptanceStatus(Request $request, string $userId)
    {
        return $this->executeService(function () use ($request, $userId) {
            $request->validate([
                'status' => 'required|in:pending,accepted,rejected,waiting',
            ]);

            $userInfo = $this->service->updateStudentAcceptanceStatus($userId, $request->status);

            return $this->okResponse(
                $userInfo,
                "Student acceptance status updated successfully"
            );
        }, 'AcceptanceExamController@updateStudentAcceptanceStatus');
    }
}
