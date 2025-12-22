<?php

namespace App\Features\Courses\Policies;

use App\Features\Courses\Models\LessonContent;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonContentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('lesson_content.viewAny');
    }

    public function view(User $user, LessonContent $lessonContent): bool
    {
        return $user->hasPermission('lesson_content.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('lesson_content.create');
    }

    public function update(User $user, LessonContent $lessonContent): bool
    {
        return $user->hasPermission('lesson_content.update');
    }

    public function delete(User $user, LessonContent $lessonContent): bool
    {
        return $user->hasPermission('lesson_content.delete');
    }

    public function restore(User $user, LessonContent $lessonContent): bool
    {
        return $user->hasPermission('lesson_content.restore');
    }

    public function forceDelete(User $user, LessonContent $lessonContent): bool
    {
        return $user->hasPermission('lesson_content.forceDelete');
    }
}
