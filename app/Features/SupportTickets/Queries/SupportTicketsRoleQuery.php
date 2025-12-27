<?php

namespace App\Features\SupportTickets\Queries;

use App\Enums\Role;
use App\Features\SupportTickets\Models\SupportTicket;
use Illuminate\Database\Eloquent\Builder;
use App\Features\SystemManagements\Models\User;

final class SupportTicketsRoleQuery
{
    public static function resolve(User $user): Builder
    {
        return match ($user->role_name) {
            // Admin sees all tickets
            Role::ADMIN => SupportTicket::query(),

            // Other roles see tickets they created OR assigned to them
            default => SupportTicket::query()
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id)           // created by user
                          ->orWhere('assigned_to', $user->id);    // assigned to user
                }),
        };
    }
}
