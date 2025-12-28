<?php

namespace App\Features\Courses\Queries;

use App\Enums\Role;
use App\Features\Courses\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class CoursesRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {
            // Student sees courses from groups they are enrolled in
            Role::STUDENT => Course::query()
                ->whereHas('groups.groupStudents', function ($query) use ($user) {
                    $query->where('student_id', $user->id)
                          ->where('status', 'active');
                }),

            // Teacher sees courses from groups they are assigned to
            Role::TEACHER => Course::query()
                ->whereHas('groups.groupTeachers', function ($query) use ($user) {
                    $query->where('teacher_id', $user->id);
                }),

            // Admin and other roles see all courses
            default => Course::query(),
        };
    }
}
