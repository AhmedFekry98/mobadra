<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Requests\EnrollStudentRequest;
use App\Features\Groups\Services\GroupStudentService;
use App\Features\Groups\Transformers\GroupStudentResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GroupStudentController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GroupStudentService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('view', $group);

            $students = $this->service->getStudentsByGroup($groupId);

            return $this->okResponse(
                GroupStudentResource::collection($students),
                "Students retrieved successfully"
            );
        }, 'GroupStudentController@index');
    }

    public function store(EnrollStudentRequest $request, string $groupId)
    {
        return $this->executeService(function () use ($request, $groupId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('update', $group);

            $result = $this->service->enrollStudent($groupId, $request->validated()['student_id']);

            if (is_array($result) && isset($result['error'])) {
                return $this->errorResponse($result['error'], 422);
            }

            return $this->okResponse(
                GroupStudentResource::make($result->load('student')),
                "Student enrolled successfully"
            );
        }, 'GroupStudentController@store');
    }

    public function destroy(string $groupId, string $studentId)
    {
        return $this->executeService(function () use ($groupId, $studentId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('update', $group);

            $this->service->removeStudent($groupId, $studentId);

            return $this->okResponse(
                null,
                "Student removed from group successfully"
            );
        }, 'GroupStudentController@destroy');
    }

    public function updateStatus(string $groupId, string $studentId)
    {
        return $this->executeService(function () use ($groupId, $studentId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('update', $group);

            $status = request('status');
            $result = $this->service->updateStudentStatus($groupId, $studentId, $status);

            if (!$result) {
                return $this->errorResponse('Student not found in group', 404);
            }

            return $this->okResponse(
                GroupStudentResource::make($result->load('student')),
                "Student status updated successfully"
            );
        }, 'GroupStudentController@updateStatus');
    }
}
