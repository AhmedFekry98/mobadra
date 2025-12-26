<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionTeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'participant_id' => $resource?->participant_id,
            'name' => $resource?->participant?->user?->name,
            'role' => $resource?->role,
            'tier' => $resource?->tier,
        ];
    }
}
