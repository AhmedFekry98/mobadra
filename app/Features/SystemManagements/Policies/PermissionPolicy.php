<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\Permission;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('permission.viewAny');
    }

    /**
     * Determine whether the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permission.view');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('permission.create');
    }

    /**
     * Determine whether the user can update the permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permission.update');
    }

    /**
     * Determine whether the user can delete the permission.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permission.delete');
    }

    /**
     * Determine whether the user can restore the permission.
     */
    public function restore(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permission.restore');
    }

    /**
     * Determine whether the user can permanently delete the permission.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasPermission('permission.forceDelete');
    }
}
