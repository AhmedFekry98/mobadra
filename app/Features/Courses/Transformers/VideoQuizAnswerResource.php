<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'selected_option_id' => $this->selected_option_id,
            'is_correct' => $this->is_correct,
            'points_earned' => $this->points_earned,
            'question' => new VideoQuizQuestionResource($this->whenLoaded('question')),
            'selected_option' => new VideoQuizOptionResource($this->whenLoaded('selectedOption')),
        ];
    }
}
