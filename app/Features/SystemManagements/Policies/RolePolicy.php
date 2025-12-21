<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role.viewAny');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission('role.view');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('role.create');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission('role.update');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission('role.delete') && 
               $user->role_id !== $role->id; // Can't delete own role
    }

    /**
     * Determine whether the user can restore the role.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermission('role.restore');
    }

    /**
     * Determine whether the user can permanently delete the role.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasPermission('role.forceDelete') && 
               $user->role_id !== $role->id; // Can't force delete own role
    }
}
