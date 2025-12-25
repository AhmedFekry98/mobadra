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
        return $user->hasPermission('groups.viewAny');
    }

    public function view(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('groups.create');
    }

    public function update(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.update');
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->hasPermission('groups.delete');
    }
}
