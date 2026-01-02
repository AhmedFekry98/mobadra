<?php

namespace App\Features\AcceptanceExams\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcceptanceExamAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attempt_id' => $this->attempt_id,
            'question_id' => $this->question_id,
            'selected_option_id' => $this->selected_option_id,
            'text_answer' => $this->text_answer,
            'is_correct' => $this->is_correct,
            'points_earned' => $this->points_earned,
            'question' => $this->whenLoaded('question', fn() => AcceptanceExamQuestionResource::make($this->question)),
            'selected_option' => $this->whenLoaded('selectedOption', fn() => AcceptanceExamQuestionOptionResource::make($this->selectedOption)),
        ];
    }
}
