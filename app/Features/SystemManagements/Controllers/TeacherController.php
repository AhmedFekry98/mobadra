<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\AuthManagement\Transformers\ProfileCollection;
use App\Features\AuthManagement\Transformers\ProfileResource;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Requests\TeacherRequest;
use App\Features\SystemManagements\Services\UserService;
use App\Features\SystemManagements\Metadata\UserMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeacherController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected UserService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of teachers.
     */
    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('viewAny', User::class);

            $result = $this->service->getTeacherUsers(
                search: $request->get('search'),
                filter: $request->get('filter'),
                sort: [['name', 'asc']],
                paginate: $request->has('page')
            );

            return $this->okResponse(
                $request->has('page')
                    ? ProfileCollection::make($result)
                    : ProfileResource::collection($result),
                "Teachers retrieved successfully"
            );
        }, 'TeacherController@index');
    }

    /**
     * Display the specified teacher.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $teacher = $this->service->getTeacherUserById($id);
            $this->authorize('view', $teacher);

            return $this->okResponse(
                ProfileResource::make($teacher),
                "Teacher retrieved successfully"
            );
        }, 'TeacherController@show');
    }

    /**
     * Store a newly created teacher.
     */
    public function store(TeacherRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', User::class);

            $teacher = $this->service->createUser($request->validated());

            return $this->okResponse(
                ProfileResource::make($teacher),
                "Teacher created successfully"
            );
        }, 'TeacherController@store');
    }

    /**
     * Update the specified teacher.
     */
    public function update(TeacherRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $teacher = $this->service->getTeacherUserById($id);
            $this->authorize('update', $teacher);

            $teacher = $this->service->updateTeacherUser($id, $request->validated());

            return $this->okResponse(
                ProfileResource::make($teacher),
                "Teacher updated successfully"
            );
        }, 'TeacherController@update');
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $teacher = $this->service->getTeacherUserById($id);
            $this->authorize('delete', $teacher);

            $this->service->deleteTeacherUser($id);

            return $this->okResponse(
                null,
                "Teacher deleted successfully"
            );
        }, 'TeacherController@destroy');
    }

    /**
     * Get metadata for teachers (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', User::class);

            return $this->okResponse(
                UserMetadata::get(),
                "Teacher metadata retrieved successfully"
            );
        }, 'TeacherController@metadata');
    }
}
