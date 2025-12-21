<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any role permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role_permission.viewAny');
    }

    /**
     * Determine whether the user can view the role permission.
     */
    public function view(User $user): bool
    {
        return $user->hasPermission('role_permission.view');
    }

    /**
     * Determine whether the user can assign permissions to roles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('role_permission.create');
    }

    /**
     * Determine whether the user can update role permissions.
     */
    public function update(User $user): bool
    {
        return $user->hasPermission('role_permission.update');
    }

    /**
     * Determine whether the user can remove permissions from roles.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermission('role_permission.delete');
    }

    /**
     * Determine whether the user can assign permissions to a specific role.
     */
    public function assignPermission(User $user, Role $role): bool
    {
        return $user->hasPermission('role_permission.create') && 
               $user->role_id !== $role->id; // Can't modify own role permissions
    }

    /**
     * Determine whether the user can remove permissions from a specific role.
     */
    public function removePermission(User $user, Role $role): bool
    {
        return $user->hasPermission('role_permission.delete') && 
               $user->role_id !== $role->id; // Can't modify own role permissions
    }
}
