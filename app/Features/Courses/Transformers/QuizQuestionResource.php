<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'quiz_id' => $resource?->quiz_id,
            'question' => $resource?->question,
            'type' => $resource?->type,
            'points' => $resource?->points,
            'order' => $resource?->order,
            'explanation' => $resource?->explanation,
            'options' => $this->whenLoaded('options', fn() => QuizQuestionOptionResource::collection($resource->options)),
            'created_at' => $resource?->created_at?->toISOString(),
        ];
    }
}
