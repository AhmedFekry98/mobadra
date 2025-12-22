<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Requests\AssignTeacherRequest;
use App\Features\Groups\Services\GroupTeacherService;
use App\Features\Groups\Transformers\GroupTeacherResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GroupTeacherController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GroupTeacherService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $this->authorize('viewAny', Group::class);

            $teachers = $this->service->getTeachersByGroup($groupId);

            return $this->okResponse(
                GroupTeacherResource::collection($teachers),
                "Teachers retrieved successfully"
            );
        }, 'GroupTeacherController@index');
    }

    public function store(AssignTeacherRequest $request, string $groupId)
    {
        return $this->executeService(function () use ($request, $groupId) {
            $this->authorize('create', Group::class);

            $data = $request->validated();
            $result = $this->service->assignTeacher(
                $groupId,
                $data['teacher_id'],
                $data['is_primary'] ?? false
            );

            if (is_array($result) && isset($result['error'])) {
                return $this->errorResponse($result['error'], 422);
            }

            return $this->okResponse(
                GroupTeacherResource::make($result->load('teacher')),
                "Teacher assigned successfully"
            );
        }, 'GroupTeacherController@store');
    }

    public function destroy(string $groupId, string $teacherId)
    {
        return $this->executeService(function () use ($groupId, $teacherId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('delete', $group);

            $this->service->removeTeacher($groupId, $teacherId);

            return $this->okResponse(
                null,
                "Teacher removed from group successfully"
            );
        }, 'GroupTeacherController@destroy');
    }

    public function setPrimary(string $groupId, string $teacherId)
    {
        return $this->executeService(function () use ($groupId, $teacherId) {
            $group = Group::findOrFail($groupId);
            $this->authorize('update', $group);

            $result = $this->service->setPrimaryTeacher($groupId, $teacherId);

            if (!$result) {
                return $this->errorResponse('Teacher not found in group', 404);
            }

            return $this->okResponse(
                GroupTeacherResource::make($result->load('teacher')),
                "Primary teacher set successfully"
            );
        }, 'GroupTeacherController@setPrimary');
    }
}
