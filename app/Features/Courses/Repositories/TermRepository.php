<?php

namespace App\Features\Courses\Repositories;

use App\Features\Courses\Models\Term;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TermRepository
{
    public function query()
    {
        return Term::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Term
    {
        return Term::find($id);
    }

    public function findOrFail(string $id): Term
    {
        return Term::findOrFail($id);
    }

    public function create(array $data): Term
    {
        return Term::create($data);
    }

    public function update(string $id, array $data): Term
    {
        $term = Term::findOrFail($id);
        $term->update($data);
        return $term->fresh();
    }

    public function delete(string $id): bool
    {
        return Term::destroy($id);
    }

    public function exists(string $id): bool
    {
        return Term::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Term::count();
    }
}
