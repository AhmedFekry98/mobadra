<?php

namespace App\Features\Community\Services;

use App\Features\Community\Models\Post;
use App\Features\Community\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        protected PostRepository $repository
    ) {}

    public function getPosts(?int $userId = null): LengthAwarePaginator
    {
        return $this->repository->getPosts($userId);
    }

    public function getPostById(int $id): Post
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function getPostWithDetails(int $id): Post
    {
        return $this->repository->getPostWithDetails($id);
    }

    public function createPost(int $userId, array $data): Post
    {
        $post = $this->repository->create([
            'user_id' => $userId,
            'content' => $data['content'],
            'visibility' => $data['visibility'] ?? 'public',
        ]);

        // Handle attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $post->addMedia($attachment)->toMediaCollection('attachments');
            }
        }

        return $post->load(['user', 'media']);
    }

    public function updatePost(int $id, array $data): Post
    {
        $post = $this->repository->update($id, [
            'content' => $data['content'],
            'visibility' => $data['visibility'] ?? 'public',
        ]);

        return $post->load(['user', 'media']);
    }

    public function deletePost(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function toggleLike(int $postId, int $userId): array
    {
        $post = $this->repository->findByIdOrFail($postId);
        $isLiked = $post->toggleLike($userId);

        return [
            'is_liked' => $isLiked,
            'likes_count' => $post->fresh()->likes_count,
        ];
    }

    public function pinPost(int $id, bool $pin = true): Post
    {
        return $this->repository->update($id, ['is_pinned' => $pin]);
    }
}
