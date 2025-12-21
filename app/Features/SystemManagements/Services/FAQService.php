<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Metadata\FAQMetadata;
use App\Features\SystemManagements\Models\FAQ;
use App\Features\SystemManagements\Repositories\FAQRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FAQService
{
    use HasGlobalQueryHandlers;

    /**
     * Inject repository in constructor
     */
    public function __construct(
        protected FAQRepository $repository
    ) {}

    /**
     * Get FAQs with global query handlers
     */
    public function getFAQs(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => FAQMetadata::getSearchableColumns(),
            'filters' => FAQMetadata::getFilters(),
            'operators' => FAQMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return collect(FAQMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    /**
     * Store FAQ
     */
    public function storeFAQ(array $data): FAQ
    {
        $creationData = collect($data)->except([
            // ignore any key?
        ])->toArray();

        // manipulate the data before creation?

        $faq = $this->repository->create($creationData);

        // write any logic after creation?

        return $faq;
    }

    /**
     * Get FAQ By Id
     */
    public function getFAQById(string $id): FAQ
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Update FAQ
     */
    public function updateFAQById(string $id, array $data): FAQ
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete FAQ
     */
    public function deleteFAQById(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Check if FAQ exists
     */
    public function FAQExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Toggle the status of the specified resource from storage.
     */
    public function toggleStatus(string $id): FAQ
    {
        return $this->repository->toggleStatus($id);
    }
}
