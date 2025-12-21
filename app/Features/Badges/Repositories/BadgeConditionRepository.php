<?php

namespace App\Features\Badges\Repositories;

use App\Features\Badges\Models\BadgeCondition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BadgeConditionRepository
{
    public function query()
    {
        return BadgeCondition::query();
    }

    /**
     * Get all BadgeConditions with optional search and pagination
     */
    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = BadgeCondition::query();
        return $paginate ? $query->paginate(config('paginate.count')) : $query->get();
    }

    public function find(string $id): ?BadgeCondition
    {
        return BadgeCondition::find($id);
    }

    public function findOrFail(string $id): BadgeCondition
    {
        return BadgeCondition::findOrFail($id);
    }

    public function create(array $data): BadgeCondition
    {
        return BadgeCondition::create($data);
    }

    public function update(string $id, array $data): BadgeCondition
    {
        $badgeCondition = BadgeCondition::findOrFail($id);
        $badgeCondition->update($data);
        return $badgeCondition->fresh();
    }

    public function delete(string $id): bool
    {
        return BadgeCondition::destroy($id);
    }

    public function getByBadgeId(string $badgeId, ?string $search = null, bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = BadgeCondition::query()->where('badge_id', $badgeId);

        if ($search) {
            $query->where('field', 'like', "%{$search}%")
                ->orWhere('operator', 'like', "%{$search}%")
                ->orWhere('value', 'like', "%{$search}%");
        }

        return $paginate ? $query->paginate(config('paginate.count')) : $query->get();
    }

    public function exists(string $id): bool
    {
        return BadgeCondition::where('id', $id)->exists();
    }

    public function count(): int
    {
        return BadgeCondition::count();
    }
}
