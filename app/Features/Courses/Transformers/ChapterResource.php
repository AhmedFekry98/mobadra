<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'course_id' => $resource?->course_id,
            'course' => $resource?->course ? new CourseResource($resource->course) : null,
            'title' => $resource?->title,
            'description' => $resource?->description,
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
