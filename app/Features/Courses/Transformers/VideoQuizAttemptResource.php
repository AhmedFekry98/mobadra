<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_quiz_id' => $this->video_quiz_id,
            'student_id' => $this->student_id,
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'score' => $this->score,
            'total_points' => $this->total_points,
            'percentage' => $this->percentage,
            'passed' => $this->passed,
            'video_quiz' => new VideoQuizResource($this->whenLoaded('videoQuiz')),
            'answers' => VideoQuizAnswerResource::collection($this->whenLoaded('answers')),
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $this->student->id,
                'name' => $this->student->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
