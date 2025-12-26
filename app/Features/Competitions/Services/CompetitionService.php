<?php

namespace App\Features\Competitions\Services;

use App\Features\Competitions\Models\Competition;
use App\Features\Competitions\Repositories\CompetitionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionService
{
    public function __construct(
        protected CompetitionRepository $repository
    ) {}

    public function getAllCompetitions(bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate, $perPage);
    }

    public function getCompetitionById(int $id): Competition
    {
        return $this->repository->findOrFail($id);
    }

    public function createCompetition(array $data): Competition
    {
        return $this->repository->create($data);
    }

    public function updateCompetition(int $id, array $data): Competition
    {
        return $this->repository->update($id, $data);
    }

    public function deleteCompetition(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->getByStatus($status);
    }

    public function search(string $term): Collection
    {
        return $this->repository->search($term);
    }

    public function updateCompetitionStatus(int $id, string $status): Competition
    {
        return $this->repository->update($id, ['status' => $status]);
    }
}
