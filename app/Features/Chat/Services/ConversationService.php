<?php

namespace App\Features\Chat\Services;

use App\Features\Chat\Models\Conversation;
use App\Features\Chat\Models\ConversationParticipant;
use App\Features\Chat\Repositories\ConversationRepository;
use App\Features\Groups\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    /**
     * Create a private conversation between teacher and student
     * Validates that both users share at least one group
     */
    public function createPrivateConversation(int $userId1, int $userId2): Conversation
    {
        // Check if conversation already exists
        $existing = $this->repository->findPrivateConversation($userId1, $userId2);
        if ($existing) {
            return $existing;
        }

        // Validate that users share at least one group (teacher-student relationship)
        if (!$this->usersShareGroup($userId1, $userId2)) {
            throw new \Exception('You can only start a conversation with teachers/students in your groups');
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

    /**
     * Check if two users share at least one group (one as teacher, one as student)
     * Optimized: Single query using UNION for better performance with large datasets
     */
    protected function usersShareGroup(int $userId1, int $userId2): bool
    {
        // Single optimized query using raw SQL with UNION
        // Checks both directions: user1 as teacher + user2 as student, OR user2 as teacher + user1 as student
        return DB::table('groups as g')
            ->join('group_teachers as gt', 'g.id', '=', 'gt.group_id')
            ->join('group_students as gs', 'g.id', '=', 'gs.group_id')
            ->where(function ($query) use ($userId1, $userId2) {
                // user1 is teacher, user2 is student
                $query->where(function ($q) use ($userId1, $userId2) {
                    $q->where('gt.teacher_id', $userId1)
                      ->where('gs.student_id', $userId2)
                      ->where('gs.status', 'active');
                })
                // OR user2 is teacher, user1 is student
                ->orWhere(function ($q) use ($userId1, $userId2) {
                    $q->where('gt.teacher_id', $userId2)
                      ->where('gs.student_id', $userId1)
                      ->where('gs.status', 'active');
                });
            })
            ->exists();
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
