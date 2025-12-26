<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionTeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'competition_id' => $resource?->competition_id,
            'name' => $resource?->name,
            'track' => $resource?->track,
            'lab' => $resource?->lab,
            'governorate' => $resource?->governorate,
            'project_title' => $resource?->project_title,
            'project_description' => $resource?->project_description,
            'phase4_score' => (float) $resource?->phase4_score,
            'hackathon_score' => (float) $resource?->hackathon_score,
            'total_score' => (float) $resource?->total_score,
            'rank' => $resource?->rank,
            'members' => $resource?->relationLoaded('members')
                ? CompetitionTeamMemberResource::collection($resource->members)
                : null,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
