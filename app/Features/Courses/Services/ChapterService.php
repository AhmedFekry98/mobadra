<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Metadata\ChapterMetadata;
use App\Features\Courses\Models\Chapter;
use App\Features\Courses\Repositories\ChapterRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChapterService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected ChapterRepository $repository
    ) {}

    public function getChapters(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->with('course');

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => ChapterMetadata::getSearchableColumns(),
            'filters' => ChapterMetadata::getFilters(),
            'operators' => ChapterMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(ChapterMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeChapter(array $data): Chapter
    {
        return $this->repository->create($data);
    }

    public function getChapterById(string $id): ?Chapter
    {
        return $this->repository->findOrFail($id);
    }

    public function updateChapter(string $id, array $data): Chapter
    {
        return $this->repository->update($id, $data);
    }

    public function deleteChapter(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getChaptersByCourseId(string $courseId): Collection
    {
        return $this->repository->getByCourseId($courseId);
    }

    public function chapterExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
