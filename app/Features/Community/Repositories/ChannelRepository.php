<?php

namespace App\Features\Community\Repositories;

use App\Features\Community\Models\Channel;
use App\Features\Grades\Models\Grade;
use App\Features\Groups\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChannelRepository
{
    public function getAll(bool $activeOnly = true, bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = Channel::query()->orderBy('sort_order');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $paginate ? $query->paginate(15) : $query->get();
    }

    public function find(int $id): ?Channel
    {
        return Channel::find($id);
    }

    public function findOrFail(int $id): Channel
    {
        return Channel::findOrFail($id);
    }

    public function findBySlug(string $slug): Channel
    {
        return Channel::where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): Channel
    {
        return Channel::create($data);
    }

    public function update(int $id, array $data): Channel
    {
        $channel = Channel::findOrFail($id);
        $channel->update($data);
        return $channel->fresh();
    }

    public function delete(int $id): bool
    {
        return Channel::destroy($id) > 0;
    }

    public function getByType(string $type): Collection
    {
        return Channel::where('type', $type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function findByGroupId(int $groupId): ?Channel
    {
        return Channel::where('type', 'group')
            ->where('channelable_type', Group::class)
            ->where('channelable_id', $groupId)
            ->first();
    }

    public function findByGradeId(int $gradeId): ?Channel
    {
        return Channel::where('type', 'grade')
            ->where('channelable_type', Grade::class)
            ->where('channelable_id', $gradeId)
            ->first();
    }


}
