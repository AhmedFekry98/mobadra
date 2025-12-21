<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Requests\AssignPermissionRequest;
use App\Features\SystemManagements\Requests\RoleRequest;
use App\Features\SystemManagements\Services\RoleService;
use App\Features\SystemManagements\Transformers\RoleCollection;
use App\Features\SystemManagements\Transformers\RoleResource;
use App\Features\SystemManagements\Metadata\RoleMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected RoleService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Role::class);

            $result = $this->service->getRoles(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ?  RoleCollection::make($result)
                    : RoleResource::collection($result),
                "Roles retrieved successfully"
            );
        }, 'RoleController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Role::class);

            $role = $this->service->createRole($request->validated());

            return $this->okResponse(
                RoleResource::make($role),
                "Role created successfully"
            );
        }, 'RoleController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $role = $this->service->getRoleById($id);
            $this->authorize('view', $role);

            return $this->okResponse(
                RoleResource::make($role),
                "Role retrieved successfully"
            );
        }, 'RoleController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $role = $this->service->getRoleById($id);
            $this->authorize('update', $role);

            $role = $this->service->updateRole($id, $request->validated());

            return $this->okResponse(
                RoleResource::make($role),
                "Role updated successfully"
            );
        }, 'RoleController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $role = $this->service->getRoleById($id);
            $this->authorize('delete', $role);

            $this->service->deleteRole($id);

            return $this->okResponse(
                null,
                "Role deleted successfully"
            );
        }, 'RoleController@destroy');
    }

    /**
     * Get metadata for roles (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Role::class);

            return $this->okResponse(
                RoleMetadata::get(),
                "Role metadata retrieved successfully"
            );
        }, 'RoleController@metadata');
    }
}
