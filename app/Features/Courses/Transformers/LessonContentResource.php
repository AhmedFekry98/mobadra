<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'lesson_id' => $resource?->lesson_id,
            'content_type' => $resource?->content_type,
            'contentable_type' => $resource?->contentable_type,
            'contentable_id' => $resource?->contentable_id,
            'contentable' => $this->formatContentable($resource),
            'title' => $resource?->title,
            'description' => $resource?->description,
            'order' => $resource?->order,
            'duration' => $resource?->duration,
            'is_required' => $resource?->is_required,
            'is_published' => $resource?->is_published,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function formatContentable($resource): ?array
    {
        if (!$resource?->contentable) {
            return null;
        }

        $contentable = $resource->contentable;

        // For video content, use signed URLs and embed (protected)
        if ($resource->content_type === 'video') {
            return [
                'id' => $contentable->id,
                'video_url' => $contentable->signed_url,
                'video_provider' => $contentable->video_provider,
                'duration' => $contentable->duration,
                'thumbnail_url' => $contentable->signed_thumbnail_url,
                'embed_url' => $contentable->embed_url,
                'embed_html' => $contentable->embed_html,
            ];
        }

        // For other content types, return as-is
        return $contentable->toArray();
    }
}
