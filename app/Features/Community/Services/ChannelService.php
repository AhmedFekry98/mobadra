<?php

namespace App\Features\Community\Services;

use App\Features\Community\Models\Channel;
use App\Features\Community\Repositories\ChannelRepository;
use App\Features\Grades\Models\Grade;
use App\Features\Groups\Models\Group;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ChannelService
{
    public function __construct(
        protected ChannelRepository $repository
    ) {}

    public function getAllChannels(User $user, bool $activeOnly = true, bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($user, $activeOnly, $paginate);
    }

    public function getChannelById(int $id): Channel
    {
        return $this->repository->findOrFail($id);
    }


    public function createChannel(array $data): Channel
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }

        return $this->repository->create($data);
    }

    public function updateChannel(int $id, array $data): Channel
    {
        return $this->repository->update($id, $data);
    }

    public function deleteChannel(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * إنشاء Channel لـ Group
     */
    public function createChannelForGroup(Group $group): Channel
    {
        return $this->repository->create([
            'name' => $group->name,
            'slug' => 'group-' . $group->id . '-' . Str::random(6),
            'description' => "Channel for group: {$group->name}",
            'type' => 'group',
            'channelable_type' => Group::class,
            'channelable_id' => $group->id,
            'is_active' => true,
            'is_private' => true,
        ]);
    }

    /**
     * إنشاء Channel لـ Grade
     */
    public function createChannelForGrade(Grade $grade): Channel
    {
        return $this->repository->create([
            'name' => "Grade {$grade->name}",
            'slug' => 'grade-' . $grade->id . '-' . Str::random(6),
            'description' => "Channel for grade: {$grade->name}",
            'type' => 'grade',
            'channelable_type' => Grade::class,
            'channelable_id' => $grade->id,
            'is_active' => true,
            'is_private' => false,
        ]);
    }

    /**
     * إنشاء Channel عام (مثل FAQ)
     */
    public function createGeneralChannel(array $data): Channel
    {
        $data['type'] = 'general';
        $data['channelable_type'] = null;
        $data['channelable_id'] = null;

        return $this->createChannel($data);
    }



    /**
     * جلب Channel الخاص بـ Group
     */
    public function getChannelForGroup(int $groupId): ?Channel
    {
        return $this->repository->findByGroupId($groupId);
    }

    /**
     * جلب Channel الخاص بـ Grade
     */
    public function getChannelForGrade(int $gradeId): ?Channel
    {
        return $this->repository->findByGradeId($gradeId);
    }


}
