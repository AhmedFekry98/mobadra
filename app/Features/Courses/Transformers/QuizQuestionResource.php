<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'quiz_id' => $resource?->quiz_id,
            'question' => $lang == 'en' ? $resource?->question : GoogleTranslateHelper::translate($resource?->question ?? '', $lang),
            'type' => $resource?->type,
            'points' => $resource?->points,
            'order' => $resource?->order,
            'explanation' => $lang == 'en' ? $resource?->explanation : GoogleTranslateHelper::translate($resource?->explanation ?? '', $lang),
            'options' => $this->whenLoaded('options', fn() => QuizQuestionOptionResource::collection($resource->options)),
            'created_at' => $resource?->created_at?->toISOString(),
        ];
    }
}
