<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\FAQ;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class FAQRepository
{
    public function query()
    {
        return FAQ::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?FAQ
    {
        return FAQ::find($id);
    }

    public function findOrFail(string $id): FAQ
    {
        return FAQ::findOrFail($id);
    }

    public function create(array $data): FAQ
    {
        return FAQ::create($data);
    }

    public function update(string $id, array $data): FAQ
    {
        $FAQ = FAQ::findOrFail($id);
        $FAQ->update($data);
        return $FAQ->fresh();
    }

    public function delete(string $id): bool
    {
        return FAQ::destroy($id);
    }

    public function getByType(string $type): Collection
    {
        return FAQ::where('type', $type)->get();
    }

    public function exists(string $id): bool
    {
        return FAQ::where('id', $id)->exists();
    }

    public function count(): int
    {
        return FAQ::count();
    }

    public function toggleStatus(string $id): FAQ
    {
        $faq = FAQ::findOrFail($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();
        return $faq->fresh();
    }

}
