<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'rank' => $resource?->rank,
            'participant_id' => $resource?->id,
            'name' => $resource?->user?->name,
            'governorate' => $resource?->governorate,
            'phase1_score' => (float) $resource?->phase1_score,
            'phase2_score' => (float) $resource?->phase2_score,
            'phase3_score' => (float) $resource?->phase3_score,
            'total_score' => (float) $resource?->total_score,
        ];
    }
}
