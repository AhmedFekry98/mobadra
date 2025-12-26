<?php

namespace App\Features\Groups\Services;

use App\Features\Community\Services\ChannelService;
use App\Features\Groups\Models\Group;
use App\Features\Groups\Repositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupService
{
    public function __construct(
        protected GroupRepository $repository,
        protected ChannelService $channelService
    ) {}

    public function getAllGroups(?bool $paginate = false, ?string $type = null): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate, $type);
    }

    public function getGroupById(string $id): ?Group
    {
        return $this->repository->findOrFail($id);
    }

    public function storeGroup(array $data): Group
    {
        $group = $this->repository->create($data);

        // إنشاء Channel تلقائياً للـ Group
        $this->channelService->createChannelForGroup($group);

        return $group;
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

    /**
     * Get available schedules for student based on their grade
     */
    public function getAvailableSchedulesForStudent(string $gradeId, string $locationType): array
    {
        return $this->repository->getAvailableSchedulesForStudent($gradeId, $locationType);
    }

    /**
     * Find a group with capacity for a specific schedule
     */
    public function findGroupWithCapacityForSchedule(
        string $gradeId,
        string $locationType,
        array $days,
        string $startTime,
        string $endTime,
        ?string $location = null
    ): ?Group {
        return $this->repository->findGroupWithCapacityForSchedule(
            $gradeId,
            $locationType,
            $days,
            $startTime,
            $endTime,
            $location
        );
    }
}
