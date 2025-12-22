<?php

namespace App\Features\Chat\Controllers;

use App\Features\Chat\Models\Conversation;
use App\Features\Chat\Requests\CreateConversationRequest;
use App\Features\Chat\Services\ConversationService;
use App\Features\Chat\Transformers\ConversationResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConversationController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected ConversationService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $type = $request->query('type');
            $userId = auth()->user()->id;

            $conversations = $this->service->getUserConversations($userId, $type);

            return $this->okResponse(
                ConversationResource::collection($conversations),
                "Conversations retrieved successfully"
            );
        }, 'ConversationController@index');
    }

    public function store(CreateConversationRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $userId = auth()->user()->id;
            $data = $request->validated();

            $conversation = $this->service->createPrivateConversation($userId, $data['participant_id']);

            return $this->okResponse(
                ConversationResource::make($conversation),
                "Conversation created successfully"
            );
        }, 'ConversationController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $conversation = $this->service->getConversationById($id);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            return $this->okResponse(
                ConversationResource::make($conversation->load(['participants.user', 'latestMessage.sender'])),
                "Conversation retrieved successfully"
            );
        }, 'ConversationController@show');
    }

    public function markAsRead(string $id)
    {
        return $this->executeService(function () use ($id) {
            $conversation = $this->service->getConversationById($id);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $this->service->markAsRead($id, $userId);

            return $this->okResponse(null, "Conversation marked as read");
        }, 'ConversationController@markAsRead');
    }

    public function mute(string $id)
    {
        return $this->executeService(function () use ($id) {
            $conversation = $this->service->getConversationById($id);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $this->service->muteConversation($id, $userId, true);

            return $this->okResponse(null, "Conversation muted");
        }, 'ConversationController@mute');
    }

    public function unmute(string $id)
    {
        return $this->executeService(function () use ($id) {
            $conversation = $this->service->getConversationById($id);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $this->service->muteConversation($id, $userId, false);

            return $this->okResponse(null, "Conversation unmuted");
        }, 'ConversationController@unmute');
    }

    public function addParticipant(Request $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $conversation = $this->service->getConversationById($id);
            $userId = auth()->user()->id;

            // Only admins can add participants to group chats
            $participant = $conversation->participants()->where('user_id', $userId)->first();
            if (!$participant || $participant->role !== 'admin') {
                return $this->errorResponse('Only admins can add participants', 403);
            }

            $this->service->addParticipant($id, $request->input('user_id'), $request->input('role', 'member'));

            return $this->okResponse(null, "Participant added successfully");
        }, 'ConversationController@addParticipant');
    }

    public function removeParticipant(string $id, string $userId)
    {
        return $this->executeService(function () use ($id, $userId) {
            $conversation = $this->service->getConversationById($id);
            $currentUserId = auth()->user()->id;

            // User can remove themselves, or admin can remove others
            $participant = $conversation->participants()->where('user_id', $currentUserId)->first();
            if ($currentUserId != $userId && (!$participant || $participant->role !== 'admin')) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->service->removeParticipant($id, $userId);

            return $this->okResponse(null, "Participant removed successfully");
        }, 'ConversationController@removeParticipant');
    }
}
