<?php

namespace App\Features\Courses\Transformers;

use App\Features\Grades\Transformers\GradeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'term' => $resource?->term ? TermResource::make($resource->term) : null,
            'grade' => $resource?->grade ? GradeResource::make($resource->grade) : null,
            'title' => $resource?->title,
            'description' => $resource?->description,
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
