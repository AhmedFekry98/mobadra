<?php

namespace App\Features\Courses\Policies;

use App\Features\Courses\Models\Term;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TermPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('term.viewAny');
    }

    public function view(User $user, Term $term): bool
    {
        return $user->hasPermission('term.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('term.create');
    }

    public function update(User $user, Term $term): bool
    {
        return $user->hasPermission('term.update');
    }

    public function delete(User $user, Term $term): bool
    {
        return $user->hasPermission('term.delete');
    }

    public function restore(User $user, Term $term): bool
    {
        return $user->hasPermission('term.restore');
    }

    public function forceDelete(User $user, Term $term): bool
    {
        return $user->hasPermission('term.forceDelete');
    }
}
