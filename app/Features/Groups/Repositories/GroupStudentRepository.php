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
}
