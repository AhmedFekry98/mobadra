<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'course' => [
                'id' => $resource?->course_id,
                'title' => $resource?->course?->title,
            ],
            'term' => [
                'id' => $resource?->course?->term_id,
                'name' => $resource?->course?->term?->name,
            ],
            "video" => [
                'count' => $resource?->contents?->where('content_type', 'video')->count() ?? 0,
                'total_duration' => $resource?->contents?->where('content_type', 'video')->sum('duration') ?? 0,
            ],
            'title' => $resource?->title,
            'description' => $resource?->description,
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
