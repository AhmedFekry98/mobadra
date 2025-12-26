<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionHackathonDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'competition_id' => $resource?->competition_id,
            'day_number' => $resource?->day_number,
            'title' => $resource?->title,
            'title_ar' => $resource?->title_ar,
            'description' => $resource?->description,
            'date' => $resource?->date?->format('Y-m-d'),
            'status' => $resource?->status,
            'level' => $resource?->level,
            'teams_count' => $resource?->teams_count,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
