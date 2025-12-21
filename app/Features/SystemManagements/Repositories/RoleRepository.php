<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository
{
    /**
     * Get query builder for roles
     */
    public function query(): Builder
    {
        return Role::query();
    }

    /**
     * Get all roles
     */
    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query()->with(['permissions']);

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Find role by ID
     */
    public function find(string $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * Find role by ID or fail
     */
    public function findOrFail(string $id): Role
    {
        return Role::findOrFail($id);
    }

    /**
     * Create new role
     */
    public function create(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update role
     */
    public function update(string $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role->fresh();
    }

    /**
     * Delete role
     */
    public function delete(string $id): bool
    {
        return Role::destroy($id) > 0;
    }

    /**
     * Get active roles
     */
    public function getActive(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = Role::where('is_active', true)
            ->with(['permissions'])
            ->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions(string $roleId, array $permissionIds): Role
    {
        $role = $this->findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
        return $role->fresh(['permissions']);
    }

    /**
     * Get role with permissions
     */
    public function getWithPermissions(string $id): Role
    {
        return Role::with(['permissions'])->findOrFail($id);
    }

    /**
     * Get role statistics
     */
    public function getStats(): array
    {
        return [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'inactive_roles' => Role::where('is_active', false)->count(),
            'roles_with_permissions' => Role::has('permissions')->count(),
        ];
    }
}
