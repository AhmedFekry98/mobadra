<?php

namespace App\Features\Groups\Queries;

use App\Enums\Role;
use App\Features\Groups\Models\GroupSession;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class GroupSessionRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {

            // show only sessions for groups where this user is a teacher
            Role::TEACHER => GroupSession::query()
                ->whereIn('group_id', function ($query) use ($user) {
                    $query->select('group_id')
                        ->from('group_teachers')
                        ->where('teacher_id', $user->id);
                }),

            // show only sessions for groups where this user is enrolled as student
            Role::STUDENT => GroupSession::query()
                ->whereIn('group_id', function ($query) use ($user) {
                    $query->select('group_id')
                        ->from('group_students')
                        ->where('student_id', $user->id);
                }),

            // admin and other roles see all sessions
            default => GroupSession::query(),
        };
    }
}
