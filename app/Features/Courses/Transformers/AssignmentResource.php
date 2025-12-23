<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'instructions' => $resource?->instructions,
            'due_date' => $resource?->due_date?->toISOString(),
            'max_score' => $resource?->max_score,
            'allow_late_submission' => $resource?->allow_late_submission,
            'late_penalty' => $resource?->late_penalty,
            'allowed_file_types' => $resource?->allowed_file_types,
            'max_file_size' => $resource?->max_file_size,
            'is_overdue' => $resource?->isOverdue(),
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
