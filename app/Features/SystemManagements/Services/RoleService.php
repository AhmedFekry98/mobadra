<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Repositories\RoleRepository;
use App\Features\SystemManagements\Metadata\RoleMetadata;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected RoleRepository $repository
    ) {}

    /**
     * Get All Roles with global query handlers
     */
    public function getRoles(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => RoleMetadata::getSearchableColumns(),
            'filters' => RoleMetadata::getFilters(),
            'operators' => RoleMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return [
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get role by ID
     */
    public function getRoleById(string $id): Role
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create new role
     */
    public function createRole(array $data): Role
    {
        // Extract permission_ids from data
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);

        $role = $this->repository->create($data);

        // Attach permissions if provided
        if (!empty($permissionIds)) {
            $role->permissions()->attach($permissionIds);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Update role
     */
    public function updateRole(string $id, array $data): Role
    {
        // Extract permission_ids from data
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);

        $role = $this->repository->update($id, $data);

        // Sync permissions if provided
        if ($permissionIds !== null) {
            $role->permissions()->sync($permissionIds);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Delete role
     */
    public function deleteRole(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get active roles
     */
    public function getActiveRoles(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getActive($paginate);
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(string $roleId, array $permissionIds): Role
    {
        return $this->repository->assignPermissions($roleId, $permissionIds);
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(string $id): Role
    {
        return $this->repository->getWithPermissions($id);
    }

    /**
     * Get role statistics
     */
    public function getRoleStats(): array
    {
        return $this->repository->getStats();
    }
}
