<?php

namespace App\Features\Courses\Repositories;

use App\Features\Courses\Models\LessonContent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LessonContentRepository
{
    public function query()
    {
        return LessonContent::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?LessonContent
    {
        return LessonContent::find($id);
    }

    public function findOrFail(string $id): LessonContent
    {
        return LessonContent::findOrFail($id);
    }

    public function create(array $data): LessonContent
    {
        return LessonContent::create($data);
    }

    public function update(string $id, array $data): LessonContent
    {
        $lessonContent = LessonContent::findOrFail($id);
        $lessonContent->update($data);
        return $lessonContent->fresh();
    }

    public function delete(string $id): bool
    {
        return LessonContent::destroy($id);
    }

    public function getByLessonId(string $lessonId): Collection
    {
        return LessonContent::where('lesson_id', $lessonId)->orderBy('order')->get();
    }

    public function exists(string $id): bool
    {
        return LessonContent::where('id', $id)->exists();
    }

    public function count(): int
    {
        return LessonContent::count();
    }
}
