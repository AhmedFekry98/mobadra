<?php

namespace App\Features\SupportTickets\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'ticket_id' => $resource?->ticket_id,
            'user_id' => $resource?->user_id,
            'user' => $this->when($resource?->relationLoaded('user'), function () use ($resource, $lang) {
                return [
                    'id' => $resource->user?->id,
                    'name' => $lang == 'en' ? $resource->user?->name : GoogleTranslateHelper::translate($resource->user?->name ?? '', $lang),
                    'email' => $resource->user?->email,
                    'image' => $resource->user?->getFirstMediaUrl('user-image'),
                ];
            }),
            'message' => $lang == 'en' ? $resource?->message : GoogleTranslateHelper::translate($resource?->message ?? '', $lang),
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
            'created_at_formatted' => $this->formatTime($resource?->created_at),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function formatTime($datetime): ?string
    {
        if (!$datetime) {
            return null;
        }

        $dayName = $datetime->format('D');
        $time = $datetime->format('g:i A');
        $diffForHumans = $datetime->diffForHumans();

        return "{$dayName}, {$time} ({$diffForHumans})";
    }
}
