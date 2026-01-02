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

    /**
     * Get available schedules for student based on grade and location type
     * Returns unique schedules (days + time) without duplicates
     * For offline groups, filters by student's governorate_id
     */
    public function getAvailableSchedulesForStudent(string $gradeId, string $locationType, ?string $governorateId = null): array
    {
        $query = Group::where('grade_id', $gradeId)
            ->where('location_type', $locationType)
            ->where('is_active', true)
            ->with(['groupStudents', 'governorate']);

        if ($locationType === 'offline' && $governorateId) {
            $query->where('governorate_id', $governorateId);
        }

        $groups = $query->get();

        // Group by schedule (days + start_time + end_time) and optionally location for offline
        $schedules = [];

        foreach ($groups as $group) {
            $scheduleKey = json_encode($group->days) . '_' . $group->start_time . '_' . $group->end_time;

            if ($locationType === 'offline') {
                $scheduleKey .= '_' . $group->location;
            }

            if (!isset($schedules[$scheduleKey])) {
                $schedules[$scheduleKey] = [
                    'days' => $group->days,
                    'start_time' => $group->start_time,
                    'end_time' => $group->end_time,
                    'location' => $locationType === 'offline' ? $group->location : null,
                    'location_map_url' => $locationType === 'offline' ? $group->location_map_url : null,
                    'governorate' => $locationType === 'offline' ? $group->governorate?->name : null,
                    'has_capacity' => false,
                ];
            }

            // If any group has capacity, mark the schedule as available
            if ($group->hasCapacity()) {
                $schedules[$scheduleKey]['has_capacity'] = true;
            }
        }

        // Return only schedules that have at least one group with capacity
        return array_values(array_filter($schedules, fn($schedule) => $schedule['has_capacity']));
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
        // Normalize days to lowercase and sort for consistent comparison
        $normalizedDays = array_map('strtolower', $days);
        sort($normalizedDays);

        $query = Group::where('grade_id', $gradeId)
            ->where('location_type', $locationType)
            ->where('start_time', $startTime)
            ->where('end_time', $endTime)
            ->where('is_active', true);

        if ($locationType === 'offline' && $location) {
            $query->where('location', $location);
        }

        // Filter by days in PHP since JSON comparison can be unreliable
        return $query->get()
            ->filter(function ($group) use ($normalizedDays) {
                $groupDays = array_map('strtolower', $group->days ?? []);
                sort($groupDays);
                return $groupDays === $normalizedDays;
            })
            ->first(fn($group) => $group->hasCapacity());
    }
}
