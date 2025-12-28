<?php

namespace App\Features\Chat\Repositories;

use App\Features\Chat\Models\Conversation;
use App\Features\Chat\Models\ConversationParticipant;
use App\Features\Chat\Queries\ConversationsRoleQuery;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    public function getUserConversations(User $user, ?string $type = null): LengthAwarePaginator
    {
        $userId = $user->id;

        // Get participant info with last_read_at for unread count calculation
        $participantSubquery = ConversationParticipant::select('conversation_id', 'last_read_at')
            ->where('user_id', $userId)
            ->whereNull('left_at');

        $query = ConversationsRoleQuery::resolve($user)
            ->select('conversations.*')
            ->joinSub($participantSubquery, 'my_participant', function ($join) {
                $join->on('conversations.id', '=', 'my_participant.conversation_id');
            })
            // Add unread count as subquery (optimized single query)
            ->addSelect([
                'unread_count' => DB::table('messages')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('messages.conversation_id', 'conversations.id')
                    ->where('messages.sender_id', '!=', $userId)
                    ->where('messages.is_deleted', false)
                    ->where(function ($q) {
                        $q->whereColumn('messages.created_at', '>', 'my_participant.last_read_at')
                            ->orWhereNull('my_participant.last_read_at');
                    })
            ])
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

    public function updateLastMessageAt(int $id): void
    {
        $this->model->where('id', $id)->update(['last_message_at' => now()]);
    }
}
