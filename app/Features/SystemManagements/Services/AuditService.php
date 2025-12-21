<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Metadata\AuditMetadata;
use App\Features\SystemManagements\Models\Audit;
use App\Features\SystemManagements\Repositories\AuditRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected AuditRepository $repository
    ) {}


    /**
     * Get All Audits with global query handlers
     */
    public function getAudits(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => AuditMetadata::getSearchableColumns(),
            'filters' => AuditMetadata::getFilters(),
            'operators' => AuditMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return array_keys(AuditMetadata::getFilters());
    }

    /**
     * Get Audit By Id
     */
    public function getAuditById(string $id): ?Audit
    {
        return $this->repository->findOrFail($id);
    }

        /**
     * Clean up old audits
     */
    public function cleanupOldAudits(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        return $this->repository->deleteOldAudits($cutoffDate);
    }
}
