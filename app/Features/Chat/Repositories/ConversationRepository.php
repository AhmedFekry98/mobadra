<?php

namespace App\Features\Chat\Repositories;

use App\Features\Chat\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ConversationRepository
{
    public function __construct(
        protected Conversation $model
    ) {}

    public function findById(int $id): ?Conversation
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int $id): Conversation
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Conversation
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Conversation
    {
        $conversation = $this->findByIdOrFail($id);
        $conversation->update($data);
        return $conversation->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findByIdOrFail($id)->delete();
    }

    public function getUserConversations(int $userId, ?string $type = null): LengthAwarePaginator
    {
        $query = $this->model
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId)->whereNull('left_at');
            })
            ->with(['latestMessage.sender', 'participants.user'])
            ->where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderByDesc('last_message_at')->paginate(20);
    }

    public function findPrivateConversation(int $userId1, int $userId2): ?Conversation
    {
        return $this->model
            ->where('type', 'private')
            ->whereHas('participants', function ($q) use ($userId1) {
                $q->where('user_id', $userId1)->whereNull('left_at');
            })
            ->whereHas('participants', function ($q) use ($userId2) {
                $q->where('user_id', $userId2)->whereNull('left_at');
            })
            ->first();
    }

    public function findGroupConversation(int $groupId): ?Conversation
    {
        return $this->model
            ->where('type', 'group')
            ->where('group_id', $groupId)
            ->first();
    }

    public function findSupportConversation(int $userId): ?Conversation
    {
        return $this->model
            ->where('type', 'support')
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId)->whereNull('left_at');
            })
            ->first();
    }

    public function updateLastMessageAt(int $id): void
    {
        $this->model->where('id', $id)->update(['last_message_at' => now()]);
    }
}
