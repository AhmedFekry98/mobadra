<?php

namespace App\Features\Competitions\Repositories;

use App\Features\Competitions\Models\CompetitionLevel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CompetitionLevelRepository
{
    public function getAll(int $competitionId, bool $paginate = false, int $perPage = 15): Collection|LengthAwarePaginator
    {
        $query = CompetitionLevel::where('competition_id', $competitionId)->orderBy('level_order');

        return $paginate ? $query->paginate($perPage) : $query->get();
    }

    public function find(int $id): ?CompetitionLevel
    {
        return CompetitionLevel::find($id);
    }

    public function findOrFail(int $id): CompetitionLevel
    {
        return CompetitionLevel::findOrFail($id);
    }

    public function create(array $data): CompetitionLevel
    {
        return CompetitionLevel::create($data);
    }

    public function update(int $id, array $data): CompetitionLevel
    {
        $level = CompetitionLevel::findOrFail($id);
        $level->update($data);
        return $level->fresh();
    }

    public function delete(int $id): bool
    {
        return CompetitionLevel::destroy($id) > 0;
    }
}
