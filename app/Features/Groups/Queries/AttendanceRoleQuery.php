<?php

namespace App\Features\Groups\Queries;

use App\Enums\Role;
use App\Features\Groups\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class AttendanceRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {

            // Teacher sees attendance for groups where they are assigned
            Role::TEACHER => Attendance::query()
                ->whereIn('group_id', function ($query) use ($user) {
                    $query->select('group_id')
                        ->from('group_teachers')
                        ->where('teacher_id', $user->id);
                }),

            // Student sees only their own attendance
            Role::STUDENT => Attendance::query()
                ->where('student_id', $user->id),

            // Admin and other roles see all attendance
            default => Attendance::query(),
        };
    }
}
