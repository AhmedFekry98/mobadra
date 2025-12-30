<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_content_id' => $this->video_content_id,
            'max_questions' => $this->max_questions,
            'passing_score' => $this->passing_score,
            'is_required' => $this->is_required,
            'is_active' => $this->is_active,
            'questions_count' => $this->whenLoaded('questions', fn() => $this->questions->count()),
            'questions' => VideoQuizQuestionResource::collection($this->whenLoaded('questions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
