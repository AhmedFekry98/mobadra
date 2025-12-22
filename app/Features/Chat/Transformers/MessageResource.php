<?php

namespace App\Features\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'conversation_id' => $resource?->conversation_id,
            'sender_id' => $resource?->sender_id,
            'sender' => $this->whenLoaded('sender', fn() => [
                'id' => $resource->sender->id,
                'name' => $resource->sender->name,
            ]),
            'reply_to_id' => $resource?->reply_to_id,
            'reply_to' => $this->whenLoaded('replyTo', fn() => [
                'id' => $resource->replyTo->id,
                'content' => $resource->replyTo->is_deleted ? 'This message was deleted' : $resource->replyTo->content,
                'sender' => [
                    'id' => $resource->replyTo->sender->id,
                    'name' => $resource->replyTo->sender->name,
                ],
            ]),
            'type' => $resource?->type,
            'content' => $resource?->is_deleted ? 'This message was deleted' : $resource?->content,
            'is_edited' => $resource?->is_edited,
            'edited_at' => $resource?->edited_at?->toISOString(),
            'is_deleted' => $resource?->is_deleted,
            'attachments' => $this->whenLoaded('media', fn() => $resource->media->map(fn($media) => [
                'id' => $media->id,
                'name' => $media->file_name,
                'url' => $media->getUrl(),
                'type' => $media->mime_type,
                'size' => $media->size,
            ])),
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
