<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupStudent;
use App\Features\Groups\Repositories\GroupRepository;
use App\Features\Groups\Repositories\GroupStudentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GroupStudentService
{
    public function __construct(
        protected GroupStudentRepository $repository,
        protected GroupRepository $groupRepository
    ) {}

    public function getStudentsByGroup(string $groupId): Collection
    {
        return $this->repository->getByGroupId($groupId);
    }

    public function getActiveStudentsByGroup(string $groupId): Collection
    {
        return $this->repository->getActiveByGroupId($groupId);
    }

    public function enrollStudent(string $groupId, string $studentId): GroupStudent|array
    {
        return DB::transaction(function () use ($groupId, $studentId) {
            $group = $this->groupRepository->findOrFail($groupId);
            $group->load('course');

            // Check if student is already in this group
            if ($this->repository->isStudentInGroup($groupId, $studentId)) {
                return ['error' => 'Student is already enrolled in this group'];
            }

            // Check if student is already enrolled in any group within the same term
            if ($group->course && $group->course->term_id) {
                $existingTermEnrollment = $this->repository->findStudentInSameTerm(
                    $studentId,
                    $group->course->term_id
                );

                if ($existingTermEnrollment) {
                    return ['error' => 'Student is already enrolled in a group for this term'];
                }
            }

            // Check if student is already enrolled in any group with same grade and location type
            $existingEnrollment = $this->repository->findStudentInSameGradeAndType(
                $studentId,
                $group->grade_id,
                $group->location_type
            );

            if ($existingEnrollment) {
                $typeLabel = $group->location_type === 'online' ? 'online' : 'offline';
                return ['error' => "Student is already enrolled in an {$typeLabel} group for this grade"];
            }

            // Check if group has capacity
            if (!$group->hasCapacity()) {
                // Try to find a similar group with capacity
                $similarGroup = $this->groupRepository->findSimilarGroupWithCapacity(
                    $group->course_id,
                    $group->days,
                    $group->start_time,
                    $group->end_time
                );

                if ($similarGroup) {
                    return $this->repository->create([
                        'group_id' => $similarGroup->id,
                        'student_id' => $studentId,
                        'enrolled_at' => now(),
                        'status' => 'active',
                    ]);
                }

                return ['error' => 'Group is full and no similar group with capacity found'];
            }

            return $this->repository->create([
                'group_id' => $groupId,
                'student_id' => $studentId,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        });
    }

    public function removeStudent(string $groupId, string $studentId): bool
    {
        return $this->repository->deleteByGroupAndStudent($groupId, $studentId);
    }

    public function updateStudentStatus(string $groupId, string $studentId, string $status): ?GroupStudent
    {
        $groupStudent = $this->repository->findByGroupAndStudent($groupId, $studentId);

        if (!$groupStudent) {
            return null;
        }

        $groupStudent->update(['status' => $status]);
        return $groupStudent->fresh();
    }

    public function isStudentInGroup(string $groupId, string $studentId): bool
    {
        return $this->repository->isStudentInGroup($groupId, $studentId);
    }

    public function countActiveStudents(string $groupId): int
    {
        return $this->repository->countActiveByGroupId($groupId);
    }

    /**
     * Enroll student in a group based on selected schedule
     * Automatically finds an available group with capacity
     */
    public function enrollStudentBySchedule(
        string $studentId,
        string $gradeId,
        string $locationType,
        array $days,
        string $startTime,
        string $endTime,
        ?string $location = null
    ): GroupStudent|array {
        return DB::transaction(function () use ($studentId, $gradeId, $locationType, $days, $startTime, $endTime, $location) {
            // Check if student is already enrolled in any group with same grade and location type
            $existingEnrollment = $this->repository->findStudentInSameGradeAndType(
                $studentId,
                $gradeId,
                $locationType
            );

            if ($existingEnrollment) {
                $typeLabel = $locationType === 'online' ? 'online' : 'offline';
                return ['error' => "Student is already enrolled in an {$typeLabel} group for this grade"];
            }

            // Find a group with capacity for this schedule
            $group = $this->groupRepository->findGroupWithCapacityForSchedule(
                $gradeId,
                $locationType,
                $days,
                $startTime,
                $endTime,
                $location
            );

            if (!$group) {
                return ['error' => 'No available group found for this schedule'];
            }

            return $this->repository->create([
                'group_id' => $group->id,
                'student_id' => $studentId,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);
        });
    }
}
