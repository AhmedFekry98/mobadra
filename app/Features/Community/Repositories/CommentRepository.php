<?php

namespace App\Features\Community\Repositories;

use App\Features\Community\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository
{
    public function __construct(
        protected Comment $model
    ) {}

    public function findById(int $id): ?Comment
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int $id): Comment
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Comment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Comment
    {
        $comment = $this->findByIdOrFail($id);
        $comment->update($data);
        return $comment->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findByIdOrFail($id)->delete();
    }

    public function getPostComments(int $postId): LengthAwarePaginator
    {
        return $this->model
            ->with(['user', 'replies.user'])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function getCommentReplies(int $commentId): LengthAwarePaginator
    {
        return $this->model
            ->with('user')
            ->where('parent_id', $commentId)
            ->orderBy('created_at')
            ->paginate(20);
    }
}
