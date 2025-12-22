<?php

namespace App\Features\Courses\Repositories;

use App\Features\Courses\Models\Chapter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChapterRepository
{
    public function query()
    {
        return Chapter::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Chapter
    {
        return Chapter::find($id);
    }

    public function findOrFail(string $id): Chapter
    {
        return Chapter::findOrFail($id);
    }

    public function create(array $data): Chapter
    {
        return Chapter::create($data);
    }

    public function update(string $id, array $data): Chapter
    {
        $chapter = Chapter::findOrFail($id);
        $chapter->update($data);
        return $chapter->fresh();
    }

    public function delete(string $id): bool
    {
        return Chapter::destroy($id);
    }

    public function getByCourseId(string $courseId): Collection
    {
        return Chapter::where('course_id', $courseId)->orderBy('order')->get();
    }

    public function exists(string $id): bool
    {
        return Chapter::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Chapter::count();
    }
}
