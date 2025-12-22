<?php

namespace App\Features\SupportTickets\Services;

use App\Features\SupportTickets\Models\SupportTicketReply;
use App\Features\SupportTickets\Repositories\SupportTicketReplyRepository;
use App\Features\SupportTickets\Repositories\SupportTicketRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SupportTicketReplyService
{
    public function __construct(
        protected SupportTicketReplyRepository $repository,
        protected SupportTicketRepository $ticketRepository
    ) {}

    public function getRepliesByTicket(string $ticketId, bool $includeInternalNotes = false): Collection
    {
        return $this->repository->getByTicketId($ticketId, $includeInternalNotes);
    }

    public function getReplyById(string $id): ?SupportTicketReply
    {
        return $this->repository->findOrFail($id);
    }

    public function storeReply(array $data): SupportTicketReply
    {
        return DB::transaction(function () use ($data) {
            $reply = $this->repository->create($data);

            // Update ticket status based on who replied
            $ticket = $this->ticketRepository->findOrFail($data['ticket_id']);

            if ($data['is_staff_reply'] ?? false) {
                // Staff replied, set to waiting for customer reply
                if ($ticket->status !== 'resolved' && $ticket->status !== 'closed') {
                    $this->ticketRepository->update($ticket->id, ['status' => 'waiting_reply']);
                }
            } else {
                // Customer replied, set to in_progress if it was waiting
                if ($ticket->status === 'waiting_reply') {
                    $this->ticketRepository->update($ticket->id, ['status' => 'in_progress']);
                }
            }

            return $reply;
        });
    }

    public function updateReply(string $id, array $data): SupportTicketReply
    {
        return $this->repository->update($id, $data);
    }

    public function deleteReply(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function countReplies(string $ticketId): int
    {
        return $this->repository->countByTicketId($ticketId);
    }
}
