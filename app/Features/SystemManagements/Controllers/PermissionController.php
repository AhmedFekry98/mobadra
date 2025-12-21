<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\Permission;
use App\Features\SystemManagements\Requests\PermissionRequest;
use App\Features\SystemManagements\Services\PermissionService;
use App\Features\SystemManagements\Transformers\PermissionCollection;
use App\Features\SystemManagements\Transformers\PermissionResource;
use App\Features\SystemManagements\Metadata\PermissionMetadata;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class PermissionController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    /**
     * Inject your service in constructor
     */
    public function __construct(
        protected PermissionService $service
    ) {
        $this->middleware('auth:sanctum');
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Permission::class);
            
            $result = $this->service->getPermissions(
                search: request('search'),
                filter: request('filter'),
                sort: request('sort'),
                paginate: request()->has('page')
            );

            return $this->okResponse(
                PermissionCollection::make($result),
                "Permissions retrieved successfully"
            );
        }, 'PermissionController@index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $this->authorize('create', Permission::class);
            
            $permission = $this->service->createPermission($request->validated());

            return $this->okResponse(
                PermissionResource::make($permission),
                "Permission created successfully"
            );
        }, 'PermissionController@store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $permission = $this->service->getPermissionById($id);
            $this->authorize('view', $permission);

            return $this->okResponse(
                PermissionResource::make($permission),
                "Permission retrieved successfully"
            );
        }, 'PermissionController@show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $permission = $this->service->getPermissionById($id);
            $this->authorize('update', $permission);
            
            $permission = $this->service->updatePermission($id, $request->validated());

            return $this->okResponse(
                PermissionResource::make($permission),
                "Permission updated successfully"
            );
        }, 'PermissionController@update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $permission = $this->service->getPermissionById($id);
            $this->authorize('delete', $permission);
            
            $this->service->deletePermission($id);

            return $this->okResponse(
                null,
                "Permission deleted successfully"
            );
        }, 'PermissionController@destroy');
    }

    /**
     * Get metadata for permissions (filters, searches, etc.)
     */
    public function metadata()
    {
        return $this->executeService(function () {
            $this->authorize('viewAny', Permission::class);

            return $this->okResponse(
                PermissionMetadata::get(),
                "Permission metadata retrieved successfully"
            );
        }, 'PermissionController@metadata');
    }
}
