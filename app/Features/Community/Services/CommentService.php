<?php

namespace App\Features\Community\Services;

use App\Features\Community\Models\Comment;
use App\Features\Community\Models\Post;
use App\Features\Community\Repositories\CommentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentService
{
    public function __construct(
        protected CommentRepository $repository
    ) {}

    public function getPostComments(int $postId): LengthAwarePaginator
    {
        return $this->repository->getPostComments($postId);
    }

    public function getCommentReplies(int $commentId): LengthAwarePaginator
    {
        return $this->repository->getCommentReplies($commentId);
    }

    public function getCommentById(int $id): Comment
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function createComment(int $postId, int $userId, array $data): Comment
    {
        $comment = $this->repository->create([
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_id' => $data['parent_id'] ?? null,
            'content' => $data['content'],
        ]);

        // Update post comments count
        Post::where('id', $postId)->increment('comments_count');

        // Update parent comment replies count if it's a reply
        if (!empty($data['parent_id'])) {
            Comment::where('id', $data['parent_id'])->increment('replies_count');
        }

        return $comment->load('user');
    }

    public function updateComment(int $id, array $data): Comment
    {
        return $this->repository->update($id, [
            'content' => $data['content'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    public function deleteComment(int $id): bool
    {
        $comment = $this->repository->findByIdOrFail($id);

        // Update post comments count
        Post::where('id', $comment->post_id)->decrement('comments_count');

        // Update parent comment replies count if it's a reply
        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->decrement('replies_count');
        }

        return $this->repository->delete($id);
    }

    public function toggleLike(int $commentId, int $userId): array
    {
        $comment = $this->repository->findByIdOrFail($commentId);
        $isLiked = $comment->toggleLike($userId);

        return [
            'is_liked' => $isLiked,
            'likes_count' => $comment->fresh()->likes_count,
        ];
    }
}
