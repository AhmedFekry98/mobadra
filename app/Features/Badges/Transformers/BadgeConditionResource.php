<?php

namespace App\Features\Badges\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BadgeConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'field' => $resource?->field,
            'operator' => $resource?->operator,
            'value' => $resource?->value,
            'badge' => BadgeResource::make($resource?->badge),
            'created_at' => $resource?->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
