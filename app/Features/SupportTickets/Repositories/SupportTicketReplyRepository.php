<?php

namespace App\Features\SupportTickets\Repositories;

use App\Features\SupportTickets\Models\SupportTicketReply;
use Illuminate\Database\Eloquent\Collection;

class SupportTicketReplyRepository
{
    public function query()
    {
        return SupportTicketReply::query();
    }

    public function getByTicketId(string $ticketId, bool $includeInternalNotes = false): Collection
    {
        $query = SupportTicketReply::where('ticket_id', $ticketId)
            ->with(['user']);

        if (!$includeInternalNotes) {
            $query->where('is_internal_note', false);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function find(string $id): ?SupportTicketReply
    {
        return SupportTicketReply::find($id);
    }

    public function findOrFail(string $id): SupportTicketReply
    {
        return SupportTicketReply::findOrFail($id);
    }

    public function create(array $data): SupportTicketReply
    {
        return SupportTicketReply::create($data);
    }

    public function update(string $id, array $data): SupportTicketReply
    {
        $reply = SupportTicketReply::findOrFail($id);
        $reply->update($data);
        return $reply->fresh();
    }

    public function delete(string $id): bool
    {
        return SupportTicketReply::destroy($id);
    }

    public function countByTicketId(string $ticketId): int
    {
        return SupportTicketReply::where('ticket_id', $ticketId)->count();
    }
}
