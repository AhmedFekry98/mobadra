<?php

namespace App\Features\Competitions\Services;

use App\Features\Competitions\Models\CompetitionLevel;
use App\Features\Competitions\Repositories\CompetitionLevelRepository;
use App\Features\Courses\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionLevelService
{
    public function __construct(
        protected CompetitionLevelRepository $repository
    ) {}

    public function getAllLevels(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($competitionId, $paginate, $perPage);
    }

    public function getLevelById(int $id): CompetitionLevel
    {
        return $this->repository->findOrFail($id);
    }

    public function createLevel(array $data): CompetitionLevel
    {
        return $this->repository->create($data);
    }

    public function updateLevel(int $id, array $data): CompetitionLevel
    {
        return $this->repository->update($id, $data);
    }

    public function deleteLevel(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getLevelCourse(int $levelId): ?Course
    {
        $level = $this->repository->findOrFail($levelId);

        if (!$level->course_slug) {
            return null;
        }

        return Course::where('slug', $level->course_slug)->first();
    }
}
