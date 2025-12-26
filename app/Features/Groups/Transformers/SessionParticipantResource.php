<?php

namespace App\Features\Groups\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'session_id' => $resource?->session_id,
            'user_id' => $resource?->user_id,
            'user' => $this->when(
                $resource?->relationLoaded('user'),
                fn() => [
                    'id' => $resource->user?->id,
                    'name' => $resource->user?->name,
                    'email' => $resource->user?->email,
                ]
            ),
            'zoom_registrant_id' => $resource?->zoom_registrant_id,
            'zoom_participant_id' => $resource?->zoom_participant_id,
            'join_url' => $resource?->join_url,
            'first_join_time' => $resource?->first_join_time?->toISOString(),
            'last_leave_time' => $resource?->last_leave_time?->toISOString(),
            'total_duration' => $resource?->total_duration,
            'status' => $resource?->status,
            'created_at' => $resource?->created_at?->toISOString(),
        ];
    }
}
