<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'lesson' => [
                'id' => $resource?->lesson?->id,
                'title' => $lang == 'en' ? $resource?->lesson?->title : GoogleTranslateHelper::translate($resource?->lesson?->title ?? '', $lang),
            ],
            'content_type' => $resource?->content_type,
            'contentable_type' => $resource?->contentable_type,
            'contentable_id' => $resource?->contentable_id,
            'contentable' => $this->formatContentable($resource, $lang),
            'title' => $lang == 'en' ? $resource?->title : GoogleTranslateHelper::translate($resource?->title ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'order' => $resource?->order,
            'duration' => $resource?->duration,
            'is_required' => $resource?->is_required,
            'is_published' => $resource?->is_published,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function formatContentable($resource, $lang): ?array
    {
        if (!$resource?->contentable) {
            return null;
        }

        $contentable = $resource->contentable;

        // For video content, use signed URLs and embed (protected)
        if ($resource->content_type === 'video') {
            return [
                'id' => $contentable->id,
                'video_url' => $contentable->video_url,
                'video_provider' => $lang == 'en' ? $contentable->video_provider : GoogleTranslateHelper::translate($contentable->video_provider ?? '', $lang),
                'duration' => $contentable->duration,
                'embed_html' => $contentable->embed_html,
            ];
        }

        // For other content types, return as-is
        return $contentable->toArray();
    }
}
