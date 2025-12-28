<?php

namespace App\Features\Groups\Policies;

use App\Features\Groups\Models\Group;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('group.viewAny');
    }

    public function view(User $user, Group $group): bool
    {
        return $user->hasPermission('group.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('group.create');
    }

    public function update(User $user, Group $group): bool
    {
        return $user->hasPermission('group.update');
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->hasPermission('group.delete');
    }

    public function restore(User $user, Group $group): bool
    {
        return $user->hasPermission('group.restore');
    }

    public function forceDelete(User $user, Group $group): bool
    {
        return $user->hasPermission('group.forceDelete');
    }
}
