<?php

namespace App\Features\Chat\Repositories;

use App\Features\Chat\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageRepository
{
    public function __construct(
        protected Message $model
    ) {}

    public function findById(int $id): ?Message
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int $id): Message
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Message
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Message
    {
        $message = $this->findByIdOrFail($id);
        $message->update($data);
        return $message->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findByIdOrFail($id)->delete();
    }

    public function softDelete(int $id): Message
    {
        $message = $this->findByIdOrFail($id);
        $message->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'content' => null,
        ]);
        return $message->fresh();
    }

    public function getConversationMessages(int $conversationId, ?int $beforeId = null): LengthAwarePaginator
    {
        $query = $this->model
            ->where('conversation_id', $conversationId)
            ->with(['sender', 'replyTo.sender', 'media'])
            ->orderByDesc('created_at');

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        return $query->paginate(50);
    }

    public function getUnreadMessages(int $conversationId, int $userId): Collection
    {
        return $this->model
            ->where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $userId)
            ->whereDoesntHave('reads', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();
    }

    public function markAsRead(int $messageId, int $userId): void
    {
        $message = $this->findByIdOrFail($messageId);
        $message->markAsReadBy($userId);
    }

    public function markConversationAsRead(int $conversationId, int $userId): void
    {
        $unreadMessages = $this->getUnreadMessages($conversationId, $userId);
        foreach ($unreadMessages as $message) {
            $message->markAsReadBy($userId);
        }
    }
}
