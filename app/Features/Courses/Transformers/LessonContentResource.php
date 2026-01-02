<?php

namespace App\Features\Courses\Transformers;

use App\Features\Groups\Models\ContentProgress;
use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        $data = [
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

        // Add content progress for students only
        if (auth()->check() && auth()->user()->role_name === 'student') {
            $data['content_progress'] = $this->getContentProgress($resource);
        }

        return $data;
    }

    protected function formatContentable($resource, $lang): ?array
    {
        if (!$resource?->contentable) {
            return null;
        }

        $contentable = $resource->contentable;

        // For video content, use signed URLs and embed (protected)
        if ($resource->content_type === 'video') {
            // Check if English video is requested
            $videoType = request('type');
            $videoUrl = $videoType === 'en' && $contentable->video_url_en
                ? $contentable->video_url_en
                : $contentable->video_url;

            return [
                'id' => $contentable->id,
                'video_url' => $contentable->video_url,
                'video_url_en' => $contentable->video_url_en,
                'video_provider' => $lang == 'en' ? $contentable->video_provider : GoogleTranslateHelper::translate($contentable->video_provider ?? '', $lang),
                'duration' => $contentable->duration,
                'embed_html' => $this->getEmbedHtmlForVideo($contentable, $videoUrl),
            ];
        }

        // For other content types, return as-is
        return $contentable->toArray();
    }

    protected function getEmbedHtmlForVideo($contentable, string $videoUrl): ?string
    {
        if ($contentable->video_provider !== 'bunny') {
            return null;
        }

        $bunnyService = app(\App\Services\BunnyStreamService::class);

        if (!$bunnyService->isConfigured()) {
            return null;
        }

        return $bunnyService->getEmbedHtml($videoUrl);
    }

    protected function getContentProgress($resource): ?array
    {
        if (!$resource?->id) {
            return null;
        }

        $userId = auth()->id();

        $progress = ContentProgress::where('user_id', $userId)
            ->where('lesson_content_id', $resource->id)
            ->first();

        return [
            'progress_percentage' => $progress?->progress_percentage ?? 0,
            'is_completed' => $progress?->is_completed ?? false,
            'last_position' => $progress?->last_position ?? 0,
            'watch_time' => $progress?->watch_time ?? 0,
        ];
    }
}
