<?php

namespace App\Features\SupportTickets\Repositories;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SupportTickets\Queries\SupportTicketsRoleQuery;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SupportTicketRepository
{
    public function query()
    {
        return SupportTicket::query();
    }

    public function getAll(User $user,?bool $paginate = false): Collection|LengthAwarePaginator
    {
          $query = SupportTicketsRoleQuery::resolve($user)
            ->with(['user', 'assignedTo', 'latestReply']);

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?SupportTicket
    {
        return SupportTicket::find($id);
    }

    public function findOrFail(string $id): SupportTicket
    {
        return SupportTicket::findOrFail($id);
    }

    public function findByTicketNumber(string $ticketNumber): ?SupportTicket
    {
        return SupportTicket::where('ticket_number', $ticketNumber)->first();
    }

    public function create(array $data): SupportTicket
    {
        return SupportTicket::create($data);
    }

    public function update(string $id, array $data): SupportTicket
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update($data);
        return $ticket->fresh();
    }

    public function delete(string $id): bool
    {
        return SupportTicket::destroy($id);
    }

    public function getByUserId(string $userId): Collection
    {
        return SupportTicket::where('user_id', $userId)
            ->with(['latestReply'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return SupportTicket::where('status', $status)
            ->with(['user', 'assignedTo'])
            ->get();
    }

    public function getAssignedTo(string $userId): Collection
    {
        return SupportTicket::where('assigned_to', $userId)
            ->with(['user', 'latestReply'])
            ->get();
    }

    public function getOpenTickets(): Collection
    {
        return SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_reply'])
            ->with(['user', 'assignedTo'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function exists(string $id): bool
    {
        return SupportTicket::where('id', $id)->exists();
    }

    public function count(): int
    {
        return SupportTicket::count();
    }
}
