<?php

namespace App\Features\Community\Controllers;

use App\Features\Community\Models\Comment;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use App\Features\Community\Requests\CreateCommentRequest;
use App\Features\Community\Requests\UpdateCommentRequest;
use App\Features\Community\Services\CommentService;
use App\Features\Community\Transformers\CommentResource;

class CommentController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected CommentService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $postId)
    {
        return $this->executeService(function () use ($postId) {
            $this->authorize('viewAny', Comment::class);

            $comments = $this->service->getPostComments($postId);

            return $this->okResponse(
                CommentResource::collection($comments),
                "Comments retrieved successfully"
            );
        }, 'CommentController@index');
    }

    public function store(CreateCommentRequest $request, string $postId)
    {
        return $this->executeService(function () use ($request, $postId) {
            $this->authorize('create', Comment::class);

            $userId = auth()->user()->id;
            $comment = $this->service->createComment($postId, $userId, $request->validated());

            return $this->okResponse(
                CommentResource::make($comment),
                "Comment created successfully"
            );
        }, 'CommentController@store');
    }

    public function update(UpdateCommentRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $comment = $this->service->getCommentById($id);
            $this->authorize('update', $comment);
            $userId = auth()->user()->id;

            if ($comment->user_id !== $userId) {
                return $this->errorResponse('You can only edit your own comments', 403);
            }

            $comment = $this->service->updateComment($id, $request->validated());

            return $this->okResponse(
                CommentResource::make($comment),
                "Comment updated successfully"
            );
        }, 'CommentController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $comment = $this->service->getCommentById($id);
            $this->authorize('delete', $comment);
            $userId = auth()->user()->id;

            if ($comment->user_id !== $userId) {
                return $this->errorResponse('You can only delete your own comments', 403);
            }

            $this->service->deleteComment($id);

            return $this->okResponse(null, "Comment deleted successfully");
        }, 'CommentController@destroy');
    }

    public function toggleLike(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->authorize('like', Comment::class);

            $userId = auth()->user()->id;
            $result = $this->service->toggleLike($id, $userId);

            return $this->okResponse(
                $result,
                $result['is_liked'] ? "Comment liked" : "Comment unliked"
            );
        }, 'CommentController@toggleLike');
    }

    public function replies(string $id)
    {
        return $this->executeService(function () use ($id) {
            $this->authorize('reply', Comment::class);

            $replies = $this->service->getCommentReplies($id);

            return $this->okResponse(
                CommentResource::collection($replies),
                "Replies retrieved successfully"
            );
        }, 'CommentController@replies');
    }
}
