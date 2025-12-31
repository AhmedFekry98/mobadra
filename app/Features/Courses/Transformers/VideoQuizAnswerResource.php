<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'question_id' => $resource?->question_id,
            'selected_option_id' => $resource?->selected_option_id,
            'is_correct' => $resource?->is_correct,
            'points_earned' => $resource?->points_earned,
            'question' => new VideoQuizQuestionResource($this->whenLoaded('question')),
            'selected_option' => new VideoQuizOptionResource($this->whenLoaded('selectedOption')),
        ];
    }
}
