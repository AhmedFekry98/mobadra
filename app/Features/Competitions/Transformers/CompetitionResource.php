<?php

namespace App\Features\Competitions\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'name' => $resource?->name,
            'description' => $resource?->description,
            'start_date' => $resource?->start_date?->format('Y-m-d'),
            'end_date' => $resource?->end_date?->format('Y-m-d'),
            'status' => $resource?->status,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
