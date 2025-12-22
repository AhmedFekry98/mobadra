<?php

namespace App\Features\Courses\Policies;

use App\Features\Courses\Models\Lesson;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('lesson.viewAny');
    }

    public function view(User $user, Lesson $lesson): bool
    {
        return $user->hasPermission('lesson.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('lesson.create');
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->hasPermission('lesson.update');
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->hasPermission('lesson.delete');
    }

    public function restore(User $user, Lesson $lesson): bool
    {
        return $user->hasPermission('lesson.restore');
    }

    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return $user->hasPermission('lesson.forceDelete');
    }
}
