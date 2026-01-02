<?php

namespace App\Features\AcceptanceExams\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcceptanceExamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'grade_id' => $this->grade_id,
            'grade' => $this->whenLoaded('grade', fn() => [
                'id' => $this->grade->id,
                'name' => $this->grade->name,
            ]),
            'title' => $this->title,
            'description' => $this->description,
            'time_limit' => $this->time_limit,
            'passing_score' => $this->passing_score,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'show_answers' => $this->show_answers,
            'is_active' => $this->is_active,
            'questions_count' => $this->whenLoaded('questions', fn() => $this->questions->count()),
            'questions' => $this->whenLoaded('questions', fn() => AcceptanceExamQuestionResource::collection($this->questions)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
