<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Repositories\GroupSessionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupSessionService
{
    public function __construct(
        protected GroupSessionRepository $repository
    ) {}

    public function getAllSessions(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate);
    }

    public function getSessionsByGroup(string $groupId, ?string $type = null): Collection
    {
        return $this->repository->getByGroupId($groupId, $type);
    }

    public function getSessionById(string $id): ?GroupSession
    {
        return $this->repository->findOrFail($id);
    }

    public function storeSession(array $data): GroupSession
    {

        return $this->repository->create($data);
    }

    public function updateSession(string $id, array $data): GroupSession
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSession(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function cancelSession(string $id, ?string $reason = null): GroupSession
    {
        return $this->repository->update($id, [
            'is_cancelled' => true,
            'cancellation_reason' => $reason,
        ]);
    }

    public function getUpcomingSessions(string $groupId): Collection
    {
        return $this->repository->getUpcomingSessions($groupId);
    }

    public function getPastSessions(string $groupId): Collection
    {
        return $this->repository->getPastSessions($groupId);
    }
}
