<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'question_id' => $resource?->question_id,
            'selected_option_id' => $resource?->selected_option_id,
            'text_answer' => $lang == 'en' ? $resource?->text_answer : GoogleTranslateHelper::translate($resource?->text_answer ?? '', $lang),
            'is_correct' => $resource?->is_correct,
            'points_earned' => $resource?->points_earned,
            'question' => $this->whenLoaded('question', fn() => QuizQuestionResource::make($resource->question)),
            'selected_option' => $this->whenLoaded('selectedOption', fn() => QuizQuestionOptionResource::make($resource->selectedOption)),
        ];
    }
}
