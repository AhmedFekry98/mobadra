<?php

namespace App\Features\Chat\Services;

use App\Features\Chat\Events\MessageSent;
use App\Features\Chat\Models\Message;
use App\Features\Chat\Repositories\ConversationRepository;
use App\Features\Chat\Repositories\MessageRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageService
{
    public function __construct(
        protected MessageRepository $repository,
        protected ConversationRepository $conversationRepository
    ) {}

    public function getConversationMessages(int $conversationId, ?int $beforeId = null): LengthAwarePaginator
    {
        return $this->repository->getConversationMessages($conversationId, $beforeId);
    }

    public function getMessageById(int $id): Message
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function sendMessage(array $data): Message
    {
        $message = $this->repository->create($data);

        // Update conversation last_message_at
        $this->conversationRepository->updateLastMessageAt($data['conversation_id']);

        // Load relationships
        $message->load(['sender', 'replyTo.sender']);

        // Broadcast message event
        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    public function editMessage(int $id, string $content): Message
    {
        return $this->repository->update($id, [
            'content' => $content,
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    public function deleteMessage(int $id): Message
    {
        return $this->repository->softDelete($id);
    }

    public function markAsRead(int $messageId, int $userId): void
    {
        $this->repository->markAsRead($messageId, $userId);
    }

    public function markConversationAsRead(int $conversationId, int $userId): void
    {
        $this->repository->markConversationAsRead($conversationId, $userId);
    }
}
