<?php

namespace App\Features\Competitions\Repositories;

use App\Features\Competitions\Models\Competition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionRepository
{
    public function getAll(bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = Competition::query()->orderBy('created_at', 'desc');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function find(int $id): ?Competition
    {
        return Competition::find($id);
    }

    public function findOrFail(int $id): Competition
    {
        return Competition::findOrFail($id);
    }

    public function create(array $data): Competition
    {
        return Competition::create($data);
    }

    public function update(int $id, array $data): Competition
    {
        $competition = Competition::findOrFail($id);
        $competition->update($data);
        return $competition->fresh();
    }

    public function delete(int $id): bool
    {
        return Competition::destroy($id) > 0;
    }

    public function getByStatus(string $status): Collection
    {
        return Competition::where('status', $status)->orderBy('start_date')->get();
    }

    public function search(string $term): Collection
    {
        return Competition::where('name', 'like', "%{$term}%")
            ->orWhere('name_ar', 'like', "%{$term}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
