<?php

namespace App\Features\Courses\Repositories;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Queries\CoursesRoleQuery;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository
{
    public function query()
    {
        return Course::query();
    }

    public function getQueryByUser(User $user): Builder
    {
        return CoursesRoleQuery::resolve($user);
    }

    public function getAll(User $user, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = CoursesRoleQuery::resolve($user);

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Course
    {
        return Course::find($id);
    }

    public function findOrFail(string $id): Course
    {
        return Course::findOrFail($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update(string $id, array $data): Course
    {
        $course = Course::findOrFail($id);
        $course->update($data);
        return $course->fresh();
    }

    public function delete(string $id): bool
    {
        return Course::destroy($id);
    }

    public function getByTermId(string $termId): Collection
    {
        return Course::where('term_id', $termId)->get();
    }

    public function exists(string $id): bool
    {
        return Course::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Course::count();
    }
}
