<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionLevelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'competition_id' => $resource?->competition_id,
            'name' => $resource?->name,
            'description' => $resource?->description,
            'level_order' => $resource?->level_order,
            'capacity' => $resource?->capacity,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
