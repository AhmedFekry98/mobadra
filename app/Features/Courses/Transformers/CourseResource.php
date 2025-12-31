<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();
        $resource = $this->resource;
        return [
            'locale' => $lang, // DEBUG: remove this after testing
            'id' => $resource?->id,
            'term' =>  [
                'id' => $resource?->term_id,
                'name' => $lang == 'en' ? $resource?->term?->name : GoogleTranslateHelper::translate($resource?->term?->name ?? '', $lang),
            ],
            'grade' => [
                'id' => $resource?->grade_id,
                'name' => $lang == 'en' ? $resource?->grade?->name : GoogleTranslateHelper::translate($resource?->grade?->name ?? '', $lang),
            ],
            'title' => $lang == 'en' ? $resource?->title : GoogleTranslateHelper::translate($resource?->title ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'slug' => $resource?->slug,
            'is_active' => $resource?->is_active,
            'image' => $this->getImageDetails($resource),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function getImageDetails($resource): ?array
    {
        $media = $resource?->getFirstMedia('course-image');

        if (!$media) {
            return null;
        }

        return [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'human_readable_size' => $this->formatBytes($media->size),
            'url' => $media->getUrl(),
        ];
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
