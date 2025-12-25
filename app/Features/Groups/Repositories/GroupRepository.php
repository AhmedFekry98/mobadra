<?php

namespace App\Features\Groups\Repositories;

use App\Features\Groups\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupRepository
{
    public function query()
    {
        return Group::query();
    }

    public function getAll(?bool $paginate = false, ?string $type = null): Collection|LengthAwarePaginator
    {
        $query = $this->query()->with(['course', 'groupStudents', 'groupTeachers']);

        if ($type) {
            $query->where('location_type', $type);
        }

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Group
    {
        return Group::find($id);
    }

    public function findOrFail(string $id): Group
    {
        return Group::findOrFail($id);
    }

    public function create(array $data): Group
    {
        return Group::create($data);
    }

    public function update(string $id, array $data): Group
    {
        $group = Group::findOrFail($id);
        $group->update($data);
        return $group->fresh();
    }

    public function delete(string $id): bool
    {
        return Group::destroy($id);
    }

    public function getByCourseId(string $courseId): Collection
    {
        return Group::where('course_id', $courseId)->get();
    }

    public function getActiveGroups(): Collection
    {
        return Group::where('is_active', true)->get();
    }

    public function findSimilarGroupWithCapacity(string $courseId, array $days, string $startTime, string $endTime): ?Group
    {
        return Group::where('course_id', $courseId)
            ->where('days', json_encode($days))
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->where('is_active', true)
            ->get()
            ->first(function ($group) {
                return $group->hasCapacity();
            });
    }

    public function exists(string $id): bool
    {
        return Group::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Group::count();
    }
}
