<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'question_id' => $resource?->question_id,
            'selected_option_id' => $resource?->selected_option_id,
            'text_answer' => $resource?->text_answer,
            'is_correct' => $resource?->is_correct,
            'points_earned' => $resource?->points_earned,
            'question' => $this->whenLoaded('question', fn() => QuizQuestionResource::make($resource->question)),
            'selected_option' => $this->whenLoaded('selectedOption', fn() => QuizQuestionOptionResource::make($resource->selectedOption)),
        ];
    }
}
