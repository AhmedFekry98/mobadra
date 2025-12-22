<?php

namespace App\Features\Courses\Repositories;

use App\Features\Courses\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LessonRepository
{
    public function query()
    {
        return Lesson::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Lesson
    {
        return Lesson::find($id);
    }

    public function findOrFail(string $id): Lesson
    {
        return Lesson::findOrFail($id);
    }

    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }

    public function update(string $id, array $data): Lesson
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->update($data);
        return $lesson->fresh();
    }

    public function delete(string $id): bool
    {
        return Lesson::destroy($id);
    }

    public function getByChapterId(string $chapterId): Collection
    {
        return Lesson::where('chapter_id', $chapterId)->orderBy('order')->get();
    }

    public function exists(string $id): bool
    {
        return Lesson::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Lesson::count();
    }
}
