<?php

namespace App\Features\Badges\Services;

use App\Features\Badges\Metadata\BadgeMetadata;
use App\Features\Badges\Models\Badge;
use App\Features\Badges\Repositories\BadgeRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BadgeService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected BadgeRepository $repository
    ) {}

    /**
     * Get All Badges with global query handlers
     */
    public function getBadges(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => BadgeMetadata::getSearchableColumns(),
            'filters' => BadgeMetadata::getFilters(),
            'operators' => BadgeMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return collect(BadgeMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    /**
     * Create Badge
     */
    public function storeBadge(array $data, ?UploadedFile $image = null): Badge
    {
        $creationData = collect($data)->except(['image'])->toArray();

        $badge = $this->repository->create($creationData);

        if ($image) {
            $badge->addMedia($image)->toMediaCollection('badge-image');
        }

        return $badge;
    }

    /**
     * Get Badge By Id
     */
    public function getBadgeById(string $id): ?Badge
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Update Badge
     */
    public function updateBadge(string $id, array $data, ?UploadedFile $image = null): Badge
    {
        // Badge is already loaded by route model binding, no need to find again

        $updateData = collect($data)->except(['image'])->toArray();
        $badge = $this->repository->update($id, $updateData);

        if ($image) {
            $badge->addMedia($image)->toMediaCollection('badge-image');
        }

        return $badge;
    }

    /**
     * Delete Badge
     */
    public function deleteBadge(string $id): bool
    {
        // Badge is already loaded by route model binding, no need to find again
        return $this->repository->delete($id);
    }

    /**
     * Get Badges by Type
     */
    public function getBadgesByType(string $type): Collection
    {
        return $this->repository->getByType($type);
    }

    /**
     * Check if Badge exists
     */
    public function badgeExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Get total badges count
     */
    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
