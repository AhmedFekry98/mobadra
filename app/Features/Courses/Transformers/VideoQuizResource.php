<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'video_content_id' => $resource?->video_content_id,
            'max_questions' => $resource?->max_questions,
            'passing_score' => $resource?->passing_score,
            'is_required' => $resource?->is_required,
            'is_active' => $resource?->is_active,
            'questions_count' => $resource?->questions?->count() ?? 0,
            'questions' => $resource?->questions ? VideoQuizQuestionResource::collection($resource->questions) : [],
            'created_at' => $resource?->created_at,
            'updated_at' => $resource?->updated_at,
        ];
    }
}
