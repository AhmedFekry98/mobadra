<?php

namespace App\Features\SupportTickets\Policies;

use App\Features\SupportTickets\Models\SupportTicketReply;
use App\Features\SystemManagements\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupportTicketReplyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('support_ticket_reply.viewAny');
    }

    public function view(User $user, SupportTicketReply $reply): bool
    {
        // User can view replies on their own tickets or has permission
        return $user->id === $reply->ticket->user_id || $user->hasPermission('support_ticket_reply.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('support_ticket_reply.create');
    }

    public function update(User $user, SupportTicketReply $reply): bool
    {
        // Author can update their own reply or has permission
        return $user->id === $reply->user_id || $user->hasPermission('support_ticket_reply.update');
    }

    public function delete(User $user, SupportTicketReply $reply): bool
    {
        return $user->hasPermission('support_ticket_reply.delete');
    }

    public function restore(User $user, SupportTicketReply $reply): bool
    {
        return $user->hasPermission('support_ticket_reply.restore');
    }

    public function forceDelete(User $user, SupportTicketReply $reply): bool
    {
        return $user->hasPermission('support_ticket_reply.forceDelete');
    }
}
