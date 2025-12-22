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
            'chapter_id' => $resource?->chapter_id,
            'chapter' => $resource?->chapter ? new ChapterResource($resource->chapter) : null,
            'title' => $resource?->title,
            'description' => $resource?->description,
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
