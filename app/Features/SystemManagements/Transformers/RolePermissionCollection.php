<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RolePermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        $groupByPermissionGroup = $request->boolean('group', false);
        $isPaginated = $request->has('page');

        // Group role permissions by role
        $groupedData = $this->collection->groupBy('role.id')->map(function ($rolePermissions, $roleId) use ($groupByPermissionGroup) {
            $role = $rolePermissions->first()->role;

            $permissions = $rolePermissions->map(function ($rolePermission) {
                return [
                    'id' => $rolePermission->permission->id,
                    'name' => $rolePermission->permission->name,
                    'caption' => $rolePermission->permission->caption,
                    'group' => $rolePermission->permission->group,
                    'createdAt' => $rolePermission->permission->created_at,
                    'updatedAt' => $rolePermission->permission->updated_at,
                ];
            });

            // Group permissions by their group if requested
            if ($groupByPermissionGroup) {
                $permissions = $permissions->groupBy('group')->map(function ($groupPermissions, $groupName) {
                    return [
                        'group' => $groupName,
                        'permissions' => $groupPermissions->values()
                    ];
                })->values();
            } else {
                $permissions = $permissions->values();
            }

            return [
                'id' => $role->id,
                'name' => $role->name,
                'caption' => $role->caption,
                'createdAt' => $role->created_at,
                'updatedAt' => $role->updated_at,
                'permissions' => $permissions
            ];
        })->values();

        // Handle pagination response
        if ($isPaginated) {
            $data = $groupByPermissionGroup && $groupedData->isNotEmpty() ? $groupedData->first() : $groupedData;

            return [
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'next_page_url' => $this->nextPageUrl(),
                'total' => $this->total(),
                'items' => $data,
            ];
        }

        // If grouping is requested and we have data, return the first role directly
        if ($groupByPermissionGroup && $groupedData->isNotEmpty()) {
            return $groupedData->first();
        }

        return $groupedData;
    }
}
