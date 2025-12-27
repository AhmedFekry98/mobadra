<?php

namespace App\Features\Community\Queries;

use App\Enums\Role;
use App\Features\Community\Models\Channel;
use App\Features\Grades\Models\Grade;
use App\Features\Groups\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class ChannelRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {
            // Admin sees all channels
            Role::ADMIN => Channel::query(),

            // Student sees: general channels + their group channels + their grade channels
            Role::STUDENT => Channel::query()
                ->where(function ($query) use ($user) {
                    // General channels (public)
                    $query->where('type', 'general')
                        // OR channels for groups the student is enrolled in
                        ->orWhere(function ($q) use ($user) {
                            $q->where('channelable_type', Group::class)
                              ->whereIn('channelable_id', function ($sub) use ($user) {
                                  $sub->select('group_id')
                                      ->from('group_students')
                                      ->where('student_id', $user->id);
                              });
                        })
                        // OR channels for the student's grade
                        ->orWhere(function ($q) use ($user) {
                            $q->where('channelable_type', Grade::class)
                              ->whereIn('channelable_id', function ($sub) use ($user) {
                                  $sub->select('grade_id')
                                      ->from('user_informations')
                                      ->where('user_id', $user->id);
                              });
                        });
                }),

            // Teacher sees: general + their teaching group channels
            Role::TEACHER => Channel::query()
                ->where(function ($query) use ($user) {
                    $query->where('type', 'general')
                        ->orWhere(function ($q) use ($user) {
                            $q->where('channelable_type', Group::class)
                              ->whereIn('channelable_id', function ($sub) use ($user) {
                                  $sub->select('group_id')
                                      ->from('group_teachers')
                                      ->where('teacher_id', $user->id);
                              });
                        });
                }),

            // Default: only general channels
            default => Channel::query()->where('type', 'general'),
        };
    }
}
