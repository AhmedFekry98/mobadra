<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupService
{
    public function __construct(
        protected GroupRepository $repository
    ) {}

    public function getAllGroups(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate);
    }

    public function getGroupById(string $id): ?Group
    {
        return $this->repository->findOrFail($id);
    }

    public function storeGroup(array $data): Group
    {
        return $this->repository->create($data);
    }

    public function updateGroup(string $id, array $data): Group
    {
        return $this->repository->update($id, $data);
    }

    public function deleteGroup(string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getGroupsByCourse(string $courseId): Collection
    {
        return $this->repository->getByCourseId($courseId);
    }

    public function getActiveGroups(): Collection
    {
        return $this->repository->getActiveGroups();
    }

    public function findSimilarGroupWithCapacity(string $courseId, array $days, string $startTime, string $endTime): ?Group
    {
        return $this->repository->findSimilarGroupWithCapacity($courseId, $days, $startTime, $endTime);
    }

    public function getLessonsByGroup(string $groupId, ?string $lessonType = null): Collection
    {
        $group = $this->repository->findOrFail($groupId);
        $query = $group->course->lessons()
            ->with(['contents'])
            ->orderBy('order');

        if ($lessonType) {
            $query->where('lesson_type', $lessonType);
        }

        return $query->get();
    }

    public function getAllGroupsLessons(?string $lessonType = null): Collection
    {
        $groups = $this->repository->getAll(false);
        $courseIds = $groups->pluck('course_id')->unique()->filter();

        $query = \App\Features\Courses\Models\Lesson::whereIn('course_id', $courseIds)
            ->with(['course.term', 'contents'])
            ->orderBy('course_id')
            ->orderBy('order');

        if ($lessonType) {
            $query->where('lesson_type', $lessonType);
        }

        return $query->get();
    }
}
