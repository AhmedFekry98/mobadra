<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Metadata\CourseMetadata;
use App\Features\Courses\Models\Course;
use App\Features\Courses\Repositories\CourseRepository;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected CourseRepository $repository
    ) {}

    public function getCourses(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->with('term');

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => CourseMetadata::getSearchableColumns(),
            'filters' => CourseMetadata::getFilters(),
            'operators' => CourseMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    protected function getSortableColumns(): array
    {
        return collect(CourseMetadata::getFilters())
            ->pluck('column')
            ->toArray();
    }

    public function storeCourse(array $data, ?UploadedFile $image = null): Course
    {
        $creationData = collect($data)->except(['image'])->toArray();
        $course = $this->repository->create($creationData);

        if ($image) {
            $course->addMedia($image)->toMediaCollection('course-image');
        }

        return $course;
    }

    public function getCourseById(string $id): ?Course
    {
        return $this->repository->findOrFail($id);
    }

    public function updateCourse(string $id, array $data, ?UploadedFile $image = null): Course
    {
        $updateData = collect($data)->except(['image'])->toArray();
        $course = $this->repository->update($id, $updateData);

        if ($image) {
            $course->addMedia($image)->toMediaCollection('course-image');
        }

        return $course;
    }

    public function deleteCourse(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getCoursesByTermId(string $termId): Collection
    {
        return $this->repository->getByTermId($termId);
    }

    public function courseExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
