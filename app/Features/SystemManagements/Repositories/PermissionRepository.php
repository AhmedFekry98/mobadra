<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository
{
    /**
     * Get query builder for permissions
     */
    public function query(): Builder
    {
        return Permission::query();
    }

    /**
     * Get all permissions
     */
    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query()->orderBy('group', 'asc')->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Find permission by ID
     */
    public function find(string $id): ?Permission
    {
        return Permission::find($id);
    }

    /**
     * Find permission by ID or fail
     */
    public function findOrFail(string $id): Permission
    {
        return Permission::findOrFail($id);
    }

    /**
     * Create new permission
     */
    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Update permission
     */
    public function update(string $id, array $data): Permission
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data);
        return $permission->fresh();
    }

    /**
     * Delete permission
     */
    public function delete(string $id): bool
    {
        return Permission::destroy($id) > 0;
    }

    /**
     * Get permissions by group
     */
    public function getByGroup(string $group, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = Permission::where('group', $group)
            ->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Get permissions grouped by group
     */
    public function getGrouped(): Collection
    {
        return Permission::orderBy('group', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('group');
    }

    /**
     * Get all permission groups
     */
    public function getGroups(): array
    {
        return Permission::distinct()
            ->orderBy('group', 'asc')
            ->pluck('group')
            ->toArray();
    }

    /**
     * Get permission statistics
     */
    public function getStats(): array
    {
        return [
            'total_permissions' => Permission::count(),
            'total_groups' => Permission::distinct('group')->count('group'),
            'permissions_by_group' => Permission::selectRaw('`group`, count(*) as count')
                ->groupBy('group')
                ->pluck('count', 'group')
                ->toArray(),
        ];
    }
}
