<?php

namespace App\Features\SupportTickets\Services;

use App\Features\SupportTickets\Models\SupportTicket;
use App\Features\SupportTickets\Repositories\SupportTicketRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SupportTicketService
{
    public function __construct(
        protected SupportTicketRepository $repository
    ) {}

    public function getAllTickets(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate);
    }

    public function getTicketById(string $id): ?SupportTicket
    {
        return $this->repository->findOrFail($id);
    }

    public function getTicketByNumber(string $ticketNumber): ?SupportTicket
    {
        return $this->repository->findByTicketNumber($ticketNumber);
    }

    public function storeTicket(array $data): SupportTicket
    {
        return $this->repository->create($data);
    }

    public function updateTicket(string $id, array $data): SupportTicket
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTicket(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getTicketsByUser(string $userId): Collection
    {
        return $this->repository->getByUserId($userId);
    }

    public function getTicketsByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function getAssignedTickets(string $userId): Collection
    {
        return $this->repository->getAssignedTo($userId);
    }

    public function getOpenTickets(): Collection
    {
        return $this->repository->getOpenTickets();
    }

    public function assignTicket(string $id, string $assigneeId): SupportTicket
    {
        return $this->repository->update($id, [
            'assigned_to' => $assigneeId,
            'status' => 'in_progress',
        ]);
    }

    public function updateStatus(string $id, string $status): SupportTicket
    {
        $data = ['status' => $status];

        if ($status === 'resolved') {
            $data['resolved_at'] = now();
        } elseif ($status === 'closed') {
            $data['closed_at'] = now();
        }

        return $this->repository->update($id, $data);
    }

    public function closeTicket(string $id): SupportTicket
    {
        return $this->updateStatus($id, 'closed');
    }

    public function resolveTicket(string $id): SupportTicket
    {
        return $this->updateStatus($id, 'resolved');
    }
}
