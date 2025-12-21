<?php

namespace App\Features\Badges\Policies;

use App\Features\Badges\Models\Badge;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BadgePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('badge.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Badge $badge): bool
    {
        return $user->hasPermission('badge.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('badge.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Badge $badge): bool
    {
        return $user->hasPermission('badge.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Badge $badge): bool
    {
        return $user->hasPermission('badge.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Badge $badge): bool
    {
        return $user->hasPermission('badge.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Badge $badge): bool
    {
        return $user->hasPermission('badge.forceDelete');
    }
}
