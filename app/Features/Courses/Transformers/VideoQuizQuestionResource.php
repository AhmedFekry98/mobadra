<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'video_quiz_id' => $resource?->video_quiz_id,
            'question' => $lang == 'en' ? $resource?->question : GoogleTranslateHelper::translate($resource?->question ?? '', $lang),
            'type' => $resource?->type,
            'points' => $resource?->points,
            'order' => $resource?->order,
            'timestamp_seconds' => $resource?->timestamp_seconds,
            'explanation' => $lang == 'en' ? $resource?->explanation : GoogleTranslateHelper::translate($resource?->explanation ?? '', $lang),
            'is_active' => $resource?->is_active,
            'options' => $this->whenLoaded('options', fn() => VideoQuizOptionResource::collection($this->options)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
