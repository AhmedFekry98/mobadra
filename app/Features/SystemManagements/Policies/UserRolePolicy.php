<?php

namespace App\Features\SystemManagements\Policies;

use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any user roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('user_role.viewAny');
    }

    /**
     * Determine whether the user can view the user role.
     */
    public function view(User $user): bool
    {
        return $user->hasPermission('user_role.view');
    }

    /**
     * Determine whether the user can assign roles to users.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('user_role.create');
    }

    /**
     * Determine whether the user can update user roles.
     */
    public function update(User $user): bool
    {
        return $user->hasPermission('user_role.update');
    }

    /**
     * Determine whether the user can remove roles from users.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermission('user_role.delete');
    }

    /**
     * Determine whether the user can assign roles to a specific user.
     */
    public function assignRole(User $user, User $targetUser): bool
    {
        return $user->hasPermission('user_role.create') && 
               $user->id !== $targetUser->id; // Can't assign role to self
    }

    /**
     * Determine whether the user can remove roles from a specific user.
     */
    public function removeRole(User $user, User $targetUser): bool
    {
        return $user->hasPermission('user_role.delete') && 
               $user->id !== $targetUser->id; // Can't remove role from self
    }
}
