<?php

namespace App\Features\Chat\Queries;

use App\Enums\Role;
use App\Features\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class ConversationsRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {
            // Student and Teacher only see conversations they are participants in
            Role::STUDENT, Role::TEACHER => Conversation::query()
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereNull('left_at');
                }),

            // All other roles (admin, etc.) see all conversations
            default => Conversation::query(),
        };
    }
}
