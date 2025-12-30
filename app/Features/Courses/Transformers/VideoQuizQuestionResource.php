<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_quiz_id' => $this->video_quiz_id,
            'question' => $this->question,
            'type' => $this->type,
            'points' => $this->points,
            'order' => $this->order,
            'timestamp_seconds' => $this->timestamp_seconds,
            'explanation' => $this->explanation,
            'is_active' => $this->is_active,
            'options' => VideoQuizOptionResource::collection($this->whenLoaded('options')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
