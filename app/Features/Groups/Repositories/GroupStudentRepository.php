<?php

namespace App\Features\Groups\Repositories;

use App\Features\Groups\Models\GroupStudent;
use Illuminate\Database\Eloquent\Collection;

class GroupStudentRepository
{
    public function query()
    {
        return GroupStudent::query();
    }

    public function getByGroupId(string $groupId): Collection
    {
        return GroupStudent::where('group_id', $groupId)
            ->with('student')
            ->get();
    }

    public function getActiveByGroupId(string $groupId): Collection
    {
        return GroupStudent::where('group_id', $groupId)
            ->where('status', 'active')
            ->with('student')
            ->get();
    }

    public function find(string $id): ?GroupStudent
    {
        return GroupStudent::find($id);
    }

    public function findOrFail(string $id): GroupStudent
    {
        return GroupStudent::findOrFail($id);
    }

    public function findByGroupAndStudent(string $groupId, string $studentId): ?GroupStudent
    {
        return GroupStudent::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function create(array $data): GroupStudent
    {
        return GroupStudent::create($data);
    }

    public function update(string $id, array $data): GroupStudent
    {
        $groupStudent = GroupStudent::findOrFail($id);
        $groupStudent->update($data);
        return $groupStudent->fresh();
    }

    public function delete(string $id): bool
    {
        return GroupStudent::destroy($id);
    }

    public function deleteByGroupAndStudent(string $groupId, string $studentId): bool
    {
        return GroupStudent::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->delete();
    }

    public function countActiveByGroupId(string $groupId): int
    {
        return GroupStudent::where('group_id', $groupId)
            ->where('status', 'active')
            ->count();
    }

    public function isStudentInGroup(string $groupId, string $studentId): bool
    {
        return GroupStudent::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->exists();
    }

    /**
     * Check if student is already enrolled in a group with same grade, location type, and term
     */
    public function findStudentInSameGradeAndType(string $studentId, string $gradeId, string $locationType, ?string $termId = null): ?GroupStudent
    {
        return GroupStudent::where('student_id', $studentId)
            ->where('status', 'active')
            ->whereHas('group', function ($q) use ($gradeId, $locationType, $termId) {
                $q->where('grade_id', $gradeId)
                    ->where('location_type', $locationType);

                if ($termId) {
                    $q->whereHas('course', function ($cq) use ($termId) {
                        $cq->where('term_id', $termId);
                    });
                }
            })
            ->with('group')
            ->first();
    }

    /**
     * Check if student is already enrolled in a group within the same term
     */
    public function findStudentInSameTerm(string $studentId, string $termId): ?GroupStudent
    {
        return GroupStudent::where('student_id', $studentId)
            ->where('status', 'active')
            ->whereHas('group.course', function ($q) use ($termId) {
                $q->where('term_id', $termId);
            })
            ->with('group.course')
            ->first();
    }

    /**
     * Check if student is already enrolled in a group with same schedule
     */
    public function findStudentInSameSchedule(
        string $studentId,
        string $gradeId,
        string $locationType,
        array $days,
        string $startTime,
        string $endTime,
        ?string $location = null
    ): ?GroupStudent {
        $query = GroupStudent::where('student_id', $studentId)
            ->where('status', 'active')
            ->whereHas('group', function ($q) use ($gradeId, $locationType, $days, $startTime, $endTime, $location) {
                $q->where('grade_id', $gradeId)
                    ->where('location_type', $locationType)
                    ->where('days', json_encode($days))
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime);

                if ($locationType === 'offline' && $location) {
                    $q->where('location', $location);
                }
            });

        return $query->first();
    }
}
