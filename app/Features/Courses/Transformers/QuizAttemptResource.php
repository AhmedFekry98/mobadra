<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'quiz_id' => $resource?->quiz_id,
            'student_id' => $resource?->student_id,
            'attempt_number' => $resource?->attempt_number,
            'status' => $resource?->status,
            'started_at' => $resource?->started_at?->toISOString(),
            'completed_at' => $resource?->completed_at?->toISOString(),
            'score' => $resource?->score,
            'total_points' => $resource?->total_points,
            'percentage' => $resource?->percentage,
            'passed' => $resource?->passed,
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $resource->student->id,
                'name' => $resource->student->name,
            ]),
            'quiz' => $this->whenLoaded('quiz', fn() => [
                'id' => $resource->quiz->id,
                'passing_score' => $resource->quiz->passing_score,
            ]),
            'answers' => $this->whenLoaded('answers', fn() => QuizAnswerResource::collection($resource->answers)),
            'created_at' => $resource?->created_at?->toISOString(),
        ];
    }
}
