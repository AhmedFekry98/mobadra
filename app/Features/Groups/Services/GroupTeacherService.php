<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\GroupTeacher;
use App\Features\Groups\Repositories\GroupTeacherRepository;
use Illuminate\Database\Eloquent\Collection;

class GroupTeacherService
{
    public function __construct(
        protected GroupTeacherRepository $repository
    ) {}

    public function getTeachersByGroup(string $groupId): Collection
    {
        return $this->repository->getByGroupId($groupId);
    }

    public function assignTeacher(string $groupId, string $teacherId, bool $isPrimary = false): GroupTeacher|array
    {
        // Check if teacher is already in this group
        if ($this->repository->isTeacherInGroup($groupId, $teacherId)) {
            return ['error' => 'Teacher is already assigned to this group'];
        }

        // If setting as primary, unset any existing primary teacher
        if ($isPrimary) {
            $existingPrimary = $this->repository->getPrimaryTeacher($groupId);
            if ($existingPrimary) {
                $existingPrimary->update(['is_primary' => false]);
            }
        }

        return $this->repository->create([
            'group_id' => $groupId,
            'teacher_id' => $teacherId,
            'assigned_at' => now(),
            'is_primary' => $isPrimary,
        ]);
    }

    public function removeTeacher(string $groupId, string $teacherId): bool
    {
        return $this->repository->deleteByGroupAndTeacher($groupId, $teacherId);
    }

    public function setPrimaryTeacher(string $groupId, string $teacherId): ?GroupTeacher
    {
        // Unset any existing primary teacher
        $existingPrimary = $this->repository->getPrimaryTeacher($groupId);
        if ($existingPrimary) {
            $existingPrimary->update(['is_primary' => false]);
        }

        $groupTeacher = $this->repository->findByGroupAndTeacher($groupId, $teacherId);
        if (!$groupTeacher) {
            return null;
        }

        $groupTeacher->update(['is_primary' => true]);
        return $groupTeacher->fresh();
    }

    public function getPrimaryTeacher(string $groupId): ?GroupTeacher
    {
        return $this->repository->getPrimaryTeacher($groupId);
    }

    public function isTeacherInGroup(string $groupId, string $teacherId): bool
    {
        return $this->repository->isTeacherInGroup($groupId, $teacherId);
    }
}
