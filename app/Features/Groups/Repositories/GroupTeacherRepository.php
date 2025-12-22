<?php

namespace App\Features\Groups\Repositories;

use App\Features\Groups\Models\GroupTeacher;
use Illuminate\Database\Eloquent\Collection;

class GroupTeacherRepository
{
    public function query()
    {
        return GroupTeacher::query();
    }

    public function getByGroupId(string $groupId): Collection
    {
        return GroupTeacher::where('group_id', $groupId)
            ->with('teacher')
            ->get();
    }

    public function find(string $id): ?GroupTeacher
    {
        return GroupTeacher::find($id);
    }

    public function findOrFail(string $id): GroupTeacher
    {
        return GroupTeacher::findOrFail($id);
    }

    public function findByGroupAndTeacher(string $groupId, string $teacherId): ?GroupTeacher
    {
        return GroupTeacher::where('group_id', $groupId)
            ->where('teacher_id', $teacherId)
            ->first();
    }

    public function create(array $data): GroupTeacher
    {
        return GroupTeacher::create($data);
    }

    public function update(string $id, array $data): GroupTeacher
    {
        $groupTeacher = GroupTeacher::findOrFail($id);
        $groupTeacher->update($data);
        return $groupTeacher->fresh();
    }

    public function delete(string $id): bool
    {
        return GroupTeacher::destroy($id);
    }

    public function deleteByGroupAndTeacher(string $groupId, string $teacherId): bool
    {
        return GroupTeacher::where('group_id', $groupId)
            ->where('teacher_id', $teacherId)
            ->delete();
    }

    public function getPrimaryTeacher(string $groupId): ?GroupTeacher
    {
        return GroupTeacher::where('group_id', $groupId)
            ->where('is_primary', true)
            ->first();
    }

    public function isTeacherInGroup(string $groupId, string $teacherId): bool
    {
        return GroupTeacher::where('group_id', $groupId)
            ->where('teacher_id', $teacherId)
            ->exists();
    }
}
