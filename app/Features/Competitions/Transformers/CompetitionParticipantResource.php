<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'competition_id' => $resource?->competition_id,
            'user_id' => $resource?->user_id,
            'name' => $resource?->user?->name,
            'email' => $resource?->user?->email,
            'governorate' => $resource?->governorate,
            'status' => $resource?->status,
            'phase1_score' => (float) $resource?->phase1_score,
            'phase2_score' => (float) $resource?->phase2_score,
            'phase3_score' => (float) $resource?->phase3_score,
            'total_score' => (float) $resource?->total_score,
            'rank' => $resource?->rank,
            'team_id' => $resource?->team_id,
            'tier' => $resource?->getTier(),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
