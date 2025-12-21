<?php

namespace App\Features\SystemManagements\Controllers;

use App\Features\SystemManagements\Models\RolePermission;
use App\Traits\ApiResponses;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Features\SystemManagements\Requests\AssignPermissionRequest;
use App\Features\SystemManagements\Transformers\RolePermissionCollection;
use App\Features\SystemManagements\Transformers\RolePermissionResource;
use App\Traits\HandleServiceExceptions;

class RolePermissionController extends Controller
{
    use ApiResponses , HandleServiceExceptions;
    private static $model = RolePermission::class;

    /**
     * assign permissions to role
     */
    public function store(AssignPermissionRequest $request)
    {
        try {
            $role = self::$model::updateOrCreate([
                "role_id" => $request->role_id,
                "permission_id" => $request->permission_id,
            ], $request->validated());

            return $this->okResponse(RolePermissionResource::make($role), "Success api call");
        } catch (\Exception $th) {
            return $this->badResponse($th->getMessage());
        }
    }

        public function getPermissionsByRoleId(Request $request, string $id)
        {
            return $this->executeService(function () use ($request, $id) {
                $query = self::$model::with(['role', 'permission'])->where("role_id", $id);

                // Apply pagination if requested
                $paginate = $request->has('page');

                if ($paginate) {
                    $result = $query->paginate(config('paginate.count'));
                } else {
                    $result = $query->get();
                }

                return $this->okResponse(
                    new RolePermissionCollection($result),
                    "Permissions retrieved successfully"
                );
            }, 'RolePermissionController@getPermissionsByRoleId');
        }
}
