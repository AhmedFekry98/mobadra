<?php

namespace App\Features\Courses\Policies;

use App\Features\Courses\Models\Course;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('course.viewAny');
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasPermission('course.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('course.create');
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasPermission('course.update');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermission('course.delete');
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->hasPermission('course.restore');
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasPermission('course.forceDelete');
    }
}
