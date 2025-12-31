<?php

namespace App\Features\SupportTickets\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportTicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'ticket_number' => $resource?->ticket_number,
            'user_id' => $resource?->user_id,
            'user' => $this->when($resource?->relationLoaded('user'), function () use ($resource, $lang) {
                return [
                    'id' => $resource->user?->id,
                    'name' => $lang == 'en' ? $resource->user?->name : GoogleTranslateHelper::translate($resource->user?->name ?? '', $lang),
                    'email' => $resource->user?->email,
                    'image' => $resource->user?->getFirstMediaUrl('user-image'),
                ];
            }),
            'subject' => $lang == 'en' ? $resource?->subject : GoogleTranslateHelper::translate($resource?->subject ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'priority' => $resource?->priority,
            'status' => $resource?->status,
            'category' => $resource?->category,
            'assigned_to' => $resource?->assigned_to,
            'assignee' => $this->when($resource?->relationLoaded('assignedTo'), function () use ($resource) {
                return $resource->assignedTo ? [
                    'id' => $resource->assignedTo->id,
                    'name' => $resource->assignedTo->name,
                    'email' => $resource->assignedTo->email,
                ] : null;
            }),
            'replies' => $this->when($resource?->relationLoaded('replies'), function () use ($resource) {
                return SupportTicketReplyResource::collection($resource->replies);
            }),
            'replies_count' => $this->when($resource?->replies_count !== null, $resource?->replies_count),
            'latest_reply' => $this->when($resource?->relationLoaded('latestReply'), function () use ($resource) {
                return $resource->latestReply ? SupportTicketReplyResource::make($resource->latestReply) : null;
            }),
            'resolved_at' => $resource?->resolved_at?->format('Y-m-d H:i:s'),
            'closed_at' => $resource?->closed_at?->format('Y-m-d H:i:s'),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
