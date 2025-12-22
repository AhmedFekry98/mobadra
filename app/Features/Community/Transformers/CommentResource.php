<?php

namespace App\Features\Community\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $userId = auth()->id();

        return [
            'id' => $resource?->id,
            'post_id' => $resource?->post_id,
            'parent_id' => $resource?->parent_id,
            'content' => $resource?->content,
            'likes_count' => $resource?->likes_count,
            'replies_count' => $resource?->replies_count,
            'is_liked' => $userId ? $resource?->isLikedBy($userId) : false,
            'is_edited' => $resource?->is_edited,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $resource->user->id,
                'name' => $resource->user->name,
                'avatar' => $resource->user->getFirstMediaUrl('avatar'),
            ]),
            'replies' => $this->whenLoaded('replies', fn() => CommentResource::collection($resource->replies)),
            'created_at' => $resource?->created_at?->toISOString(),
            'created_at_human' => $resource?->created_at?->diffForHumans(),
            'edited_at' => $resource?->edited_at?->toISOString(),
        ];
    }
}
