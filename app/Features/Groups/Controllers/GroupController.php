<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Requests\GroupRequest;
use App\Features\Groups\Services\GroupService;
use App\Features\Groups\Transformers\GroupCollection;
use App\Features\Groups\Transformers\GroupResource;
use App\Features\Groups\Metadata\GroupMetadata;
use App\Features\Courses\Transformers\LessonResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GroupController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GroupService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Group::class);

            $result = $this->service->getAllGroups(
                paginate: request()->has('page'),
                type: request('type')
            );

            return $this->okResponse(
                request()->has('page')
                    ? GroupCollection::make($result)
                    : GroupResource::collection($result),
                "Success"
            );
        }, 'GroupController@index');
    }

    public function store(GroupRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Group::class);

            $group = $this->service->storeGroup($request->validated());

            return $this->okResponse(
                GroupResource::make($group),
                "Group created successfully"
            );
        }, 'GroupController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $group = $this->service->getGroupById($id);
            $this->authorize('view', $group);

            return $this->okResponse(
                GroupResource::make($group->load(['course', 'groupStudents.student', 'groupTeachers.teacher', 'sessions'])),
                "Group retrieved successfully"
            );
        }, 'GroupController@show');
    }

    public function update(GroupRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $group = $this->service->getGroupById($id);
            $this->authorize('update', $group);

            $group = $this->service->updateGroup($id, $request->validated());

            return $this->okResponse(
                GroupResource::make($group),
                "Group updated successfully"
            );
        }, 'GroupController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $group = $this->service->getGroupById($id);
            $this->authorize('delete', $group);

            $this->service->deleteGroup($id);

            return $this->okResponse(
                null,
                "Group deleted successfully"
            );
        }, 'GroupController@destroy');
    }

    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Group::class);

            return $this->okResponse(
                GroupMetadata::get(),
                "Group metadata retrieved successfully"
            );
        }, 'GroupController@metadata');
    }

    /**
     * Get available schedules for student based on their grade
     * For online groups: returns unique schedules (days + time)
     * For offline groups: returns unique schedules (days + time + location)
     */
    public function availableSchedules()
    {
        return $this->executeService(function () {
            $user = auth()->user();
            $gradeId = $user->userInformation?->grade_id;

            if (!$gradeId) {
                return $this->errorResponse(
                    "Student grade not found. Please update your profile.",
                    422
                );
            }

            $locationType = request('type'); // 'online' or 'offline'

            if (!$locationType || !in_array($locationType, ['online', 'offline'])) {
                return $this->errorResponse(
                    "Invalid location type. Must be 'online' or 'offline'.",
                    422
                );
            }

            $schedules = $this->service->getAvailableSchedulesForStudent($gradeId, $locationType);

            return $this->okResponse(
                $schedules,
                "Available schedules retrieved successfully"
            );
        }, 'GroupController@availableSchedules');
    }

}
