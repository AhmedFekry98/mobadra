<?php

namespace App\Features\Groups\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'user_id' => $resource?->user_id,
            'lesson_content_id' => $resource?->lesson_content_id,
            'group_id' => $resource?->group_id,
            'progress_percentage' => $resource?->progress_percentage,
            'watch_time' => $resource?->watch_time,
            'last_position' => $resource?->last_position,
            'is_completed' => $resource?->is_completed,
            'completed_at' => $resource?->completed_at?->toISOString(),
            'last_watched_at' => $resource?->last_watched_at?->toISOString(),
            'lesson_content' => $this->when(
                $resource?->relationLoaded('lessonContent'),
                fn() => [
                    'id' => $resource->lessonContent?->id,
                    'title' => $resource->lessonContent?->title,
                    'content_type' => $resource->lessonContent?->content_type,
                ]
            ),
        ];
    }
}
