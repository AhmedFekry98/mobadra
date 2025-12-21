<?php

namespace App\Features\Badges\Policies;

use App\Features\Badges\Models\BadgeCondition;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BadgeConditionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('badge_condition.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BadgeCondition $badgeCondition): bool
    {
        return $user->hasPermission('badge_condition.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('badge_condition.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BadgeCondition $badgeCondition): bool
    {
        return $user->hasPermission('badge_condition.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BadgeCondition $badgeCondition): bool
    {
        return $user->hasPermission('badge_condition.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BadgeCondition $badgeCondition): bool
    {
        return $user->hasPermission('badge_condition.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BadgeCondition $badgeCondition): bool
    {
        return $user->hasPermission('badge_condition.forceDelete');
    }
}
