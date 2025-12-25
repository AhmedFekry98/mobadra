<?php

namespace App\Features\Community\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $userId = auth()->id();

        return [
            'id' => $resource?->id,
            'channel' => $resource?->channel ? [
                'id' => $resource->channel->id,
                'name' => $resource->channel->name,
                'slug' => $resource->channel->slug,
            ] : null,
            'content' => $resource?->content,
            'visibility' => $resource?->visibility,
            'is_pinned' => $resource?->is_pinned,
            'likes_count' => $resource?->likes_count,
            'comments_count' => $resource?->comments_count,
            'is_liked' => $userId ? $resource?->isLikedBy($userId) : false,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $resource->user->id,
                'name' => $resource->user->name,
                'avatar' => $resource->user->getFirstMediaUrl('avatar'),
            ]),
            'attachments' => $resource?->getMedia('attachments')->map(fn($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'extension' => $media->extension,
            ]),
            'created_at' => $resource?->created_at?->toISOString(),
            'created_at_human' => $resource?->created_at?->diffForHumans(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
