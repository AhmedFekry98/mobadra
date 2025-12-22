<?php

namespace App\Features\Chat\Services;

use App\Features\Chat\Models\Conversation;
use App\Features\Chat\Models\ConversationParticipant;
use App\Features\Chat\Repositories\ConversationRepository;
use App\Features\Groups\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;

class ConversationService
{
    public function __construct(
        protected ConversationRepository $repository
    ) {}

    public function getUserConversations(int $userId, ?string $type = null): LengthAwarePaginator
    {
        return $this->repository->getUserConversations($userId, $type);
    }

    public function getConversationById(int $id): Conversation
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function createPrivateConversation(int $userId1, int $userId2): Conversation
    {
        // Check if conversation already exists
        $existing = $this->repository->findPrivateConversation($userId1, $userId2);
        if ($existing) {
            return $existing;
        }

        $conversation = $this->repository->create([
            'type' => 'private',
            'created_by' => $userId1,
            'is_active' => true,
        ]);

        // Add participants
        $this->addParticipant($conversation->id, $userId1, 'member');
        $this->addParticipant($conversation->id, $userId2, 'member');

        return $conversation->load('participants.user');
    }

    public function createGroupConversation(int $groupId, int $createdBy): Conversation
    {
        // Check if conversation already exists for this group
        $existing = $this->repository->findGroupConversation($groupId);
        if ($existing) {
            return $existing;
        }

        $group = Group::findOrFail($groupId);

        $conversation = $this->repository->create([
            'type' => 'group',
            'name' => $group->name . ' Chat',
            'group_id' => $groupId,
            'created_by' => $createdBy,
            'is_active' => true,
        ]);

        // Add all group students and teachers as participants
        foreach ($group->groupStudents()->where('status', 'active')->get() as $student) {
            $this->addParticipant($conversation->id, $student->student_id, 'member');
        }

        foreach ($group->groupTeachers as $teacher) {
            $this->addParticipant($conversation->id, $teacher->teacher_id, 'admin');
        }

        return $conversation->load('participants.user');
    }

    public function createSupportConversation(int $userId): Conversation
    {
        // Check if user already has a support conversation
        $existing = $this->repository->findSupportConversation($userId);
        if ($existing) {
            return $existing;
        }

        $conversation = $this->repository->create([
            'type' => 'support',
            'name' => 'Support Chat',
            'created_by' => $userId,
            'is_active' => true,
        ]);

        // Add user as participant
        $this->addParticipant($conversation->id, $userId, 'member');

        return $conversation->load('participants.user');
    }

    public function addParticipant(int $conversationId, int $userId, string $role = 'member'): ConversationParticipant
    {
        return ConversationParticipant::updateOrCreate(
            [
                'conversation_id' => $conversationId,
                'user_id' => $userId,
            ],
            [
                'role' => $role,
                'joined_at' => now(),
                'left_at' => null,
            ]
        );
    }

    public function removeParticipant(int $conversationId, int $userId): void
    {
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['left_at' => now()]);
    }

    public function updateConversation(int $id, array $data): Conversation
    {
        return $this->repository->update($id, $data);
    }

    public function deleteConversation(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function markAsRead(int $conversationId, int $userId): void
    {
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    public function muteConversation(int $conversationId, int $userId, bool $mute = true): void
    {
        ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update(['is_muted' => $mute]);
    }
}
