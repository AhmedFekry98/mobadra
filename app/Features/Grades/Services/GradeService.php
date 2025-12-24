<?php

namespace App\Features\Grades\Services;

use App\Features\Grades\Metadata\GradeMetadata;
use App\Features\Grades\Models\Grade;
use App\Features\Grades\Repositories\GradeRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GradeService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected GradeRepository $repository
    ) {}

    public function getGrades(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => GradeMetadata::getSearchableColumns(),
            'filters' => GradeMetadata::getFilters(),
            'operators' => GradeMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(GradeMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeGrade(array $data): Grade
    {
        return $this->repository->create($data);
    }

    public function getGradeById(string $id): ?Grade
    {
        return $this->repository->findOrFail($id);
    }

    public function updateGrade(string $id, array $data): Grade
    {
        return $this->repository->update($id, $data);
    }

    public function deleteGrade(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getActiveGrades(): Collection
    {
        return $this->repository->getActive();
    }

    public function gradeExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
