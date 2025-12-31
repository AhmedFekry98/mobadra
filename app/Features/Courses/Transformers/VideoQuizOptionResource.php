<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'option_text' => $lang == 'en' ? $resource?->option_text : GoogleTranslateHelper::translate($resource?->option_text ?? '', $lang),
            'is_correct' => $resource?->is_correct,
            'order' => $resource?->order,
        ];
    }
}
