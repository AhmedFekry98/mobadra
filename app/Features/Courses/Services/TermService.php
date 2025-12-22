<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Metadata\TermMetadata;
use App\Features\Courses\Models\Term;
use App\Features\Courses\Repositories\TermRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TermService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected TermRepository $repository
    ) {}

    public function getTerms(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => TermMetadata::getSearchableColumns(),
            'filters' => TermMetadata::getFilters(),
            'operators' => TermMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(TermMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeTerm(array $data): Term
    {
        return $this->repository->create($data);
    }

    public function getTermById(string $id): ?Term
    {
        return $this->repository->findOrFail($id);
    }

    public function updateTerm(string $id, array $data): Term
    {
        return $this->repository->update($id, $data);
    }

    public function deleteTerm(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function termExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
