<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoQuizAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'video_quiz_id' => $resource?->video_quiz_id,
            'student_id' => $resource?->student_id,
            'status' => $resource?->status,
            'started_at' => $resource?->started_at,
            'completed_at' => $resource?->completed_at,
            'score' => $resource?->score,
            'total_points' => $resource?->total_points,
            'percentage' => $resource?->percentage,
            'passed' => $resource?->passed,
            'video_quiz' => $this->whenLoaded('videoQuiz', fn() => new VideoQuizResource($this->videoQuiz)),
            'answers' => $this->whenLoaded('answers', fn() => VideoQuizAnswerResource::collection($this->answers)),
            'student' => $this->whenLoaded('student', fn() => [
                'id' => $resource->student->id,
                'name' => $lang == 'en' ? $resource->student->name : GoogleTranslateHelper::translate($resource->student->name ?? '', $lang),
            ]),
            'created_at' => $resource?->created_at,
        ];
    }
}
