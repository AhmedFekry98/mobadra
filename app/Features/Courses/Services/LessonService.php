<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Metadata\LessonMetadata;
use App\Features\Courses\Models\Lesson;
use App\Features\Courses\Repositories\LessonRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LessonService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected LessonRepository $repository
    ) {}

    public function getLessons(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->with('course');

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => LessonMetadata::getSearchableColumns(),
            'filters' => LessonMetadata::getFilters(),
            'operators' => LessonMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(LessonMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeLesson(array $data): Lesson
    {
        return $this->repository->create($data);
    }

    public function getLessonById(string $id): ?Lesson
    {
        return $this->repository->findOrFail($id);
    }

    public function updateLesson(string $id, array $data): Lesson
    {
        return $this->repository->update($id, $data);
    }

    public function deleteLesson(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getLessonsByCourseId(string $courseId): Collection
    {
        return $this->repository->getByCourseId($courseId);
    }

    public function lessonExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
