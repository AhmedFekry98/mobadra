<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Models\Permission;
use App\Features\SystemManagements\Repositories\PermissionRepository;
use App\Features\SystemManagements\Metadata\PermissionMetadata;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected PermissionRepository $repository
    ) {}

    /**
     * Get All Permissions with global query handlers
     */
    public function getPermissions(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => PermissionMetadata::getSearchableColumns(),
            'filters' => PermissionMetadata::getFilters(),
            'operators' => PermissionMetadata::getOperators(),
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
            'caption',
            'group',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get permission by ID
     */
    public function getPermissionById(string $id): Permission
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create new permission
     */
    public function createPermission(array $data): Permission
    {
        return $this->repository->create($data);
    }

    /**
     * Update permission
     */
    public function updatePermission(string $id, array $data): Permission
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete permission
     */
    public function deletePermission(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get permissions by group
     */
    public function getPermissionsByGroup(string $group, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getByGroup($group, $paginate);
    }

    /**
     * Get permissions grouped by group
     */
    public function getGroupedPermissions(): Collection
    {
        return $this->repository->getGrouped();
    }

    /**
     * Get all permission groups
     */
    public function getPermissionGroups(): array
    {
        return $this->repository->getGroups();
    }

    /**
     * Get permission statistics
     */
    public function getPermissionStats(): array
    {
        return $this->repository->getStats();
    }
}
