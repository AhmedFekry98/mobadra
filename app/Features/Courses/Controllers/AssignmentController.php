<?php

namespace App\Features\Courses\Controllers;

use App\Features\Courses\Models\Assignment;
use App\Features\Courses\Requests\AddAssignmentFilesRequest;
use App\Features\Courses\Requests\CreateSubmissionRequest;
use App\Features\Courses\Requests\GradeSubmissionRequest;
use App\Features\Courses\Services\AssignmentService;
use App\Features\Courses\Transformers\AssignmentResource;
use App\Features\Courses\Transformers\AssignmentSubmissionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class AssignmentController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected AssignmentService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function createSubmission(string $assignmentId, CreateSubmissionRequest $request)
    {
        return $this->executeService(function () use ($assignmentId, $request) {
            $studentId = auth()->user()->id;
            $submission = $this->service->createSubmission($assignmentId, $studentId, $request->validated());

            return $this->okResponse(
                AssignmentSubmissionResource::make($submission),
                "Submission saved as draft"
            );
        }, 'AssignmentController@createSubmission');
    }

    public function submitAssignment(string $submissionId)
    {
        return $this->executeService(function () use ($submissionId) {
            $submission = $this->service->submitAssignment($submissionId);

            return $this->okResponse(
                AssignmentSubmissionResource::make($submission),
                "Assignment submitted successfully"
            );
        }, 'AssignmentController@submitAssignment');
    }

    public function submissions(string $assignmentId)
    {
        return $this->executeService(function () use ($assignmentId) {
            $submissions = $this->service->getAssignmentSubmissions($assignmentId);

            return $this->okResponse(
                AssignmentSubmissionResource::collection($submissions),
                "Submissions retrieved successfully"
            );
        }, 'AssignmentController@submissions');
    }

    public function mySubmission(string $assignmentId)
    {
        return $this->executeService(function () use ($assignmentId) {
            $studentId = auth()->user()->id;
            $submission = $this->service->getStudentSubmission($assignmentId, $studentId);

            return $this->okResponse(
                $submission ? AssignmentSubmissionResource::make($submission) : null,
                "Submission retrieved"
            );
        }, 'AssignmentController@mySubmission');
    }

    public function myAssignments()
    {
        return $this->executeService(function () {
            $studentId = auth()->user()->id;
            $assignments = $this->service->getStudentAssignments($studentId);

            return $this->okResponse(
                AssignmentSubmissionResource::collection($assignments),
                "Assignments retrieved"
            );
        }, 'AssignmentController@myAssignments');
    }

    public function getFiles(string $assignmentId)
    {
        return $this->executeService(function () use ($assignmentId) {
            $files = $this->service->getFiles($assignmentId);

            return $this->okResponse(
                $files,
                "Files retrieved successfully"
            );
        }, 'AssignmentController@getFiles');
    }

    public function addFiles(string $assignmentId, AddAssignmentFilesRequest $request)
    {
        return $this->executeService(function () use ($assignmentId, $request) {
            $assignment = $this->service->addFiles($assignmentId, $request->validated());

            return $this->okResponse(
                AssignmentResource::make($assignment),
                "Files added successfully"
            );
        }, 'AssignmentController@addFiles');
    }

    public function removeFile(string $assignmentId, string $mediaId)
    {
        return $this->executeService(function () use ($assignmentId, $mediaId) {
            $assignment = $this->service->removeFile($assignmentId, $mediaId);

            return $this->okResponse(
                AssignmentResource::make($assignment),
                "File removed successfully"
            );
        }, 'AssignmentController@removeFile');
    }
}
