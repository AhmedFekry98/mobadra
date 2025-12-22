<?php

namespace App\Features\Chat\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'user_id' => $resource?->user_id,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $resource->user->id,
                'name' => $resource->user->name,
                'email' => $resource->user->email,
            ]),
            'role' => $resource?->role,
            'joined_at' => $resource?->joined_at?->toISOString(),
            'last_read_at' => $resource?->last_read_at?->toISOString(),
            'is_muted' => $resource?->is_muted,
            'is_online' => false, // Can be implemented with presence channels
        ];
    }
}
