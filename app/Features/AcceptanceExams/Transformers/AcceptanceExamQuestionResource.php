<?php

namespace App\Features\AcceptanceExams\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcceptanceExamQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'acceptance_exam_id' => $this->acceptance_exam_id,
            'question' => $this->question,
            'type' => $this->type,
            'points' => $this->points,
            'order' => $this->order,
            'explanation' => $this->explanation,
            'is_active' => $this->is_active,
            'options' => AcceptanceExamQuestionOptionResource::collection($this->whenLoaded('options')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
