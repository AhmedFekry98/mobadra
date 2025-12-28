<?php

namespace App\Features\Chat\Controllers;

use App\Features\Chat\Events\UserTyping;
use App\Features\Chat\Models\Message;
use App\Features\Chat\Requests\SendMessageRequest;
use App\Features\Chat\Requests\EditMessageRequest;
use App\Features\Chat\Services\ConversationService;
use App\Features\Chat\Services\MessageService;
use App\Features\Chat\Transformers\MessageResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected MessageService $service,
        protected ConversationService $conversationService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $conversationId, Request $request)
    {
        return $this->executeService(function () use ($conversationId, $request) {
            $conversation = $this->conversationService->getConversationById($conversationId);
            $this->authorize('view', $conversation);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $beforeId = $request->query('before_id');
            $messages = $this->service->getConversationMessages($conversationId, $beforeId);

            return $this->okResponse(
                MessageResource::collection($messages),
                "Messages retrieved successfully"
            );
        }, 'MessageController@index');
    }

    public function store(SendMessageRequest $request, string $conversationId)
    {
        return $this->executeService(function () use ($request, $conversationId) {
            $this->authorize('create', Message::class);
            $conversation = $this->conversationService->getConversationById($conversationId);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $data = $request->validated();
            $data['conversation_id'] = $conversationId;
            $data['sender_id'] = $userId;

            $message = $this->service->sendMessage($data);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $message->addMedia($file)->toMediaCollection('attachments');
                }
                $message->load('media');
            }

            return $this->okResponse(
                MessageResource::make($message),
                "Message sent successfully"
            );
        }, 'MessageController@store');
    }

    public function update(EditMessageRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $message = $this->service->getMessageById($id);
            $this->authorize('update', $message);
            $userId = auth()->user()->id;

            if ($message->sender_id !== $userId) {
                return $this->errorResponse('You can only edit your own messages', 403);
            }

            $message = $this->service->editMessage($id, $request->input('content'));

            return $this->okResponse(
                MessageResource::make($message->load('sender')),
                "Message updated successfully"
            );
        }, 'MessageController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $message = $this->service->getMessageById($id);
            $this->authorize('delete', $message);
            $userId = auth()->user()->id;

            // User can delete their own messages, or admin can delete any
            $conversation = $this->conversationService->getConversationById($message->conversation_id);
            $participant = $conversation->participants()->where('user_id', $userId)->first();

            if ($message->sender_id !== $userId && (!$participant || $participant->role !== 'admin')) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->service->deleteMessage($id);

            return $this->okResponse(null, "Message deleted successfully");
        }, 'MessageController@destroy');
    }

    public function markAsRead(string $conversationId)
    {
        return $this->executeService(function () use ($conversationId) {
            $conversation = $this->conversationService->getConversationById($conversationId);
            $userId = auth()->user()->id;

            if (!$conversation->hasParticipant($userId)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            $this->service->markConversationAsRead($conversationId, $userId);
            $this->conversationService->markAsRead($conversationId, $userId);

            return $this->okResponse(null, "Messages marked as read");
        }, 'MessageController@markAsRead');
    }

    public function typing(string $conversationId)
    {
        return $this->executeService(function () use ($conversationId) {
            $conversation = $this->conversationService->getConversationById($conversationId);
            $user = auth()->user();

            if (!$conversation->hasParticipant($user->id)) {
                return $this->errorResponse('You are not a participant in this conversation', 403);
            }

            broadcast(new UserTyping($conversationId, $user->id, $user->name, true))->toOthers();

            return $this->okResponse(null, "Typing indicator sent");
        }, 'MessageController@typing');
    }
}
