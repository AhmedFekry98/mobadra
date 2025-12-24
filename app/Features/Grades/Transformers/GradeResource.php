<?php

namespace App\Features\Grades\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'name' => $resource?->name,
            'code' => $resource?->code,
            'description' => $resource?->description,
            'min_age' => $resource?->min_age,
            'max_age' => $resource?->max_age,
            'age_range' => $resource?->age_range,
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
