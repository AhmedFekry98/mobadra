<?php

namespace App\Features\AcceptanceExams\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcceptanceExamAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'acceptance_exam_id' => $this->acceptance_exam_id,
            'student_id' => $this->student_id,
            'attempt_number' => $this->attempt_number,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'score' => $this->score,
            'total_points' => $this->total_points,
            'percentage' => $this->percentage,
            'passed' => $this->passed,
            'acceptance_exam' => $this->whenLoaded('acceptanceExam', fn() => AcceptanceExamResource::make($this->acceptanceExam)),
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'email' => $this->student->email,
            ]),
            'answers' => $this->whenLoaded('answers', fn() => AcceptanceExamAnswerResource::collection($this->answers)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
