<?php

namespace App\Features\SupportTickets\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'ticket_id' => $resource?->ticket_id,
            'user_id' => $resource?->user_id,
            'user' => $this->when($resource?->relationLoaded('user'), function () use ($resource) {
                return [
                    'id' => $resource->user?->id,
                    'name' => $resource->user?->name,
                    'email' => $resource->user?->email,
                    'image' => $resource->user?->getFirstMediaUrl('user-image'),
                ];
            }),
            'message' => $resource?->message,
            'is_staff_reply' => $resource?->is_staff_reply,
            'is_internal_note' => $resource?->is_internal_note,
            'attachments' => $resource?->getMedia('attachments')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'size' => $media->size,
                    'mime_type' => $media->mime_type,
                ];
            }),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
