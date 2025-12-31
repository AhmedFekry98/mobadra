<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $userId = auth()->id();
        $lang = app()->getLocale();

        return [
            'id' => $resource?->id,
            'time_limit' => $resource?->time_limit,
            'title' => $lang == 'en' ? $resource?->pivot?->title : GoogleTranslateHelper::translate($resource?->pivot?->title ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->pivot?->description : GoogleTranslateHelper::translate($resource?->pivot?->description ?? '', $lang),
            'order' => $resource?->pivot?->order ?? null,
            'is_active' => $resource?->pivot?->is_active ?? null,
            'passing_score' => $resource?->passing_score,
            'max_attempts' => $resource?->max_attempts,
            'shuffle_questions' => $resource?->shuffle_questions,
            'show_answers' => $resource?->show_answers,
            'questions_count' => $resource?->questions?->count() ?? 0,
            'total_points' => $resource?->questions?->sum('points') ?? 0,
            'can_attempt' => $userId ? $resource?->canStudentAttempt($userId) : false,
            'questions' => $this->whenLoaded('questions', fn() => QuizQuestionResource::collection($resource->questions)),
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
