<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Requests\GroupRequest;
use App\Features\Groups\Services\GroupService;
use App\Features\Groups\Transformers\GroupCollection;
use App\Features\Groups\Transformers\GroupResource;
use App\Features\Groups\Metadata\GroupMetadata;
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
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? new GroupCollection($result)
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

    public function getByCourse(string $courseId)
    {
        return $this->executeService(function () use ($courseId) {
            $this->authorize('viewAny', Group::class);

            $groups = $this->service->getGroupsByCourse($courseId);

            return $this->okResponse(
                GroupResource::collection($groups),
                "Groups retrieved successfully"
            );
        }, 'GroupController@getByCourse');
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
}
