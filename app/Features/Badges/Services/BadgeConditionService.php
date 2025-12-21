<?php

namespace App\Features\Badges\Services;

use App\Features\Badges\Metadata\BadgeConditionMetadata;
use App\Features\Badges\Models\BadgeCondition;
use App\Features\Badges\Repositories\BadgeConditionRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BadgeConditionService
{
    use HasGlobalQueryHandlers;

    /**
     * Inject repository in constructor
     */
    public function __construct(
        protected BadgeConditionRepository $repository
    ) {}

    /**
     * Get BadgeConditions with global query handlers
     */
    public function getBadgeConditions(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => BadgeConditionMetadata::getSearchableColumns(),
            'filters' => BadgeConditionMetadata::getFilters(),
            'operators' => BadgeConditionMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return collect(BadgeConditionMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    /**
     * Store BadgeCondition
     */
    public function storeBadgeCondition(array $data): BadgeCondition
    {
        $creationData = collect($data)->except([
            // ignore any key?
        ])->toArray();

        // manipulate the data before creation?

        $badgeCondition = $this->repository->create($creationData);

        // write any logic after creation?

        return $badgeCondition;
    }

    /**
     * Get BadgeCondition By Id
     */
    public function getBadgeConditionById(string $id): BadgeCondition
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Update BadgeCondition
     */
    public function updateBadgeConditionById(string $id, array $data): BadgeCondition
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete BadgeCondition
     */
    public function deleteBadgeConditionById(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get BadgeConditions by Badge ID
     */
    public function getBadgeConditionsByBadgeId(string $badgeId, ?string $search = null, bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getByBadgeId($badgeId, $search, $paginate);
    }

    /**
     * Check if BadgeCondition exists
     */
    public function badgeConditionExists(string $id): bool
    {
        return $this->repository->exists($id);
    }
}
