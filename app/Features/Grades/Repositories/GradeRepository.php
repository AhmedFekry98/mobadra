<?php

namespace App\Features\Grades\Repositories;

use App\Features\Grades\Models\Grade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GradeRepository
{
    public function query()
    {
        return Grade::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query()->orderBy('order');

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Grade
    {
        return Grade::find($id);
    }

    public function findOrFail(string $id): Grade
    {
        return Grade::findOrFail($id);
    }

    public function create(array $data): Grade
    {
        return Grade::create($data);
    }

    public function update(string $id, array $data): Grade
    {
        $grade = Grade::findOrFail($id);
        $grade->update($data);
        return $grade->fresh();
    }

    public function delete(string $id): bool
    {
        return Grade::destroy($id);
    }

    public function getActive(): Collection
    {
        return Grade::where('is_active', true)->orderBy('order')->get();
    }

    public function exists(string $id): bool
    {
        return Grade::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Grade::count();
    }
}
