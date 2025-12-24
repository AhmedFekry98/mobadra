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
            'course_id' => $resource?->course_id,
            'title' => $resource?->title,
            'description' => $resource?->description,
            'lesson_type' => $resource?->lesson_type,
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'contents' => $resource?->contents ? LessonContentResource::collection($resource->contents) : [],
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
