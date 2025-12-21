<?php

namespace App\Features\Badges\Repositories;

use App\Features\Badges\Models\Badge;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BadgeRepository
{
    public function query()
    {
        return Badge::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Badge
    {
        return Badge::find($id);
    }

    public function findOrFail(string $id): Badge
    {
        return Badge::findOrFail($id);
    }

    public function create(array $data): Badge
    {
        return Badge::create($data);
    }

    public function update(string $id, array $data): Badge
    {
        $badge = Badge::findOrFail($id);
        $badge->update($data);
        return $badge->fresh();
    }

    public function delete(string $id): bool
    {
        return Badge::destroy($id);
    }

    public function getByType(string $type): Collection
    {
        return Badge::where('type', $type)->get();
    }

    public function exists(string $id): bool
    {
        return Badge::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Badge::count();
    }
}
