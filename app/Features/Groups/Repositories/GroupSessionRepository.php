<?php

namespace App\Features\Groups\Repositories;

use App\Features\Groups\Models\GroupSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupSessionRepository
{
    public function query()
    {
        return GroupSession::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query()->with(['group', 'lessonContent']);

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function getByGroupId(string $groupId): Collection
    {
        return GroupSession::where('group_id', $groupId)
            ->orderBy('session_date')
            ->get();
    }

    public function find(string $id): ?GroupSession
    {
        return GroupSession::find($id);
    }

    public function findOrFail(string $id): GroupSession
    {
        return GroupSession::findOrFail($id);
    }

    public function create(array $data): GroupSession
    {
        return GroupSession::create($data);
    }

    public function update(string $id, array $data): GroupSession
    {
        $session = GroupSession::findOrFail($id);
        $session->update($data);
        return $session->fresh();
    }

    public function delete(string $id): bool
    {
        return GroupSession::destroy($id);
    }

    public function getUpcomingSessions(string $groupId): Collection
    {
        return GroupSession::where('group_id', $groupId)
            ->where('session_date', '>=', now()->toDateString())
            ->where('is_cancelled', false)
            ->orderBy('session_date')
            ->get();
    }

    public function getPastSessions(string $groupId): Collection
    {
        return GroupSession::where('group_id', $groupId)
            ->where('session_date', '<', now()->toDateString())
            ->orderBy('session_date', 'desc')
            ->get();
    }
}
