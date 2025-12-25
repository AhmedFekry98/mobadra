<?php

namespace App\Features\Community\Repositories;

use App\Features\Community\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function __construct(
        protected Post $model
    ) {}

    public function findById(int $id): ?Post
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int $id): Post
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Post
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Post
    {
        $post = $this->findByIdOrFail($id);
        $post->update($data);
        return $post->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findByIdOrFail($id)->delete();
    }

    public function getPosts(?int $userId = null, bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->model
            ->with(['user', 'media', 'channel'])
            ->where('is_active', true)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $paginate ? $query->paginate(15) : $query->get();
    }

    public function getPostWithDetails(int $id): Post
    {
        return $this->model
            ->with([
                'user',
                'media',
                'rootComments' => function ($query) {
                    $query->with(['user', 'replies.user'])
                        ->orderByDesc('created_at')
                        ->limit(10);
                }
            ])
            ->findOrFail($id);
    }
}
