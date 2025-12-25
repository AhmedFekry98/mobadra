<?php

namespace App\Features\Groups\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'group' =>[
                'id' => $resource?->group?->id,
                'name' => $resource?->group?->name,
            ],
            'session_date' => $resource?->session_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'topic' => $resource?->topic,
            'lesson' => [
                'id' => $resource?->lesson?->id,
                'title' => $resource?->lesson?->title,
            ],
            'is_cancelled' => $resource?->is_cancelled,
            'cancellation_reason' => $resource?->cancellation_reason,
            'meeting_provider' => $resource?->meeting_provider,
            'meeting_id' => $resource?->meeting_id,
            'has_meeting' => !empty($resource?->moderator_link) || !empty($resource?->attendee_link),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
