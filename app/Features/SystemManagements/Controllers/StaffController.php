<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\AuthManagement\Transformers\ProfileCollection;
use App\Features\AuthManagement\Transformers\ProfileResource;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Requests\StaffRequest;
use App\Features\SystemManagements\Services\UserService;
use App\Features\SystemManagements\Transformers\StaffCollection;
use App\Features\SystemManagements\Transformers\StaffResource;
use App\Features\SystemManagements\Metadata\UserMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
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
     * Display a listing of staff members.
     */
    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('viewAny', User::class);
            
            $result = $this->service->getStaffUsers(
                search: $request->get('search'),
                filter: $request->get('role') ? [['role.name', '=', $request->get('role')]] : null,
                sort: [['name', 'asc']],
                paginate: $request->has('page')
            );

            return $this->okResponse(
                $request->has('page')
                    ? ProfileCollection::make($result)
                    : ProfileResource::collection($result),
                "Staff retrieved successfully"
            );
        }, 'StaffController@index');
    }

    /**
     * Display the specified staff member.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $staff = $this->service->getStaffUserById($id);
            $this->authorize('view', $staff);

            return $this->okResponse(
                ProfileResource::make($staff),
                "Staff member retrieved successfully"
            );
        }, 'StaffController@show');
    }

    /**
     * Store a newly created staff member.
     */
    public function store(StaffRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', User::class);
            
            $staff = $this->service->createUser($request->validated());

            return $this->okResponse(
                ProfileResource::make($staff),
                "Staff member created successfully"
            );
        }, 'StaffController@store');
    }

    /**
     * Update the specified staff member.
     */
    public function update(StaffRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $staff = $this->service->getStaffUserById($id);
            $this->authorize('update', $staff);
            
            $staff = $this->service->updateStaffUser($id, $request->validated());

            return $this->okResponse(
                ProfileResource::make($staff),
                "Staff member updated successfully"
            );
        }, 'StaffController@update');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $staff = $this->service->getStaffUserById($id);
            $this->authorize('delete', $staff);
            
            $this->service->deleteStaffUser($id);

            return $this->okResponse(
                null,
                "Staff member deleted successfully"
            );
        }, 'StaffController@destroy');
    }

    /**
     * Get metadata for staff users (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', User::class);

            return $this->okResponse(
                UserMetadata::get(),
                "Staff metadata retrieved successfully"
            );
        }, 'StaffController@metadata');
    }
}
