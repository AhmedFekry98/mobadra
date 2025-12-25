<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'option_text' => $resource?->option_text,
            'order' => $resource?->order,
            'is_correct' => (bool) $resource?->is_correct,
        ];
    }
}
