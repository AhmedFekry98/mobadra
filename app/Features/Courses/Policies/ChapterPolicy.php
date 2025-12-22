<?php

namespace App\Features\Courses\Policies;

use App\Features\Courses\Models\Chapter;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChapterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('chapter.viewAny');
    }

    public function view(User $user, Chapter $chapter): bool
    {
        return $user->hasPermission('chapter.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('chapter.create');
    }

    public function update(User $user, Chapter $chapter): bool
    {
        return $user->hasPermission('chapter.update');
    }

    public function delete(User $user, Chapter $chapter): bool
    {
        return $user->hasPermission('chapter.delete');
    }

    public function restore(User $user, Chapter $chapter): bool
    {
        return $user->hasPermission('chapter.restore');
    }

    public function forceDelete(User $user, Chapter $chapter): bool
    {
        return $user->hasPermission('chapter.forceDelete');
    }
}
