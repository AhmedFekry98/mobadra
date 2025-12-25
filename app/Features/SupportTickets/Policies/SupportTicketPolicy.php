<?php

namespace App\Features\SupportTickets\Policies;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupportTicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('support_tickets.viewAny');
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        // User can view their own tickets or staff can view all
        return $user->id === $ticket->user_id || $user->hasPermission('support_tickets.viewAny');
    }

    public function create(User $user): bool
    {
        // Any authenticated user can create a ticket
        return true;
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        // Only staff can update tickets
        return $user->hasPermission('support_tickets.update');
    }

    public function delete(User $user, SupportTicket $ticket): bool
    {
        return $user->hasPermission('support_tickets.delete');
    }
}
