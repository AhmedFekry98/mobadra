<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'course' => [
                'id' => $resource?->course_id,
                'title' => $lang == 'en' ? $resource?->course?->title : GoogleTranslateHelper::translate($resource?->course?->title ?? '', $lang),
            ],
            'term' => [
                'id' => $resource?->course?->term_id,
                'name' => $lang == 'en' ? $resource?->course?->term?->name : GoogleTranslateHelper::translate($resource?->course?->term?->name ?? '', $lang),
            ],
            "video" => [
                'count' => $resource?->contents?->where('content_type', 'video')->count() ?? 0,
                'total_duration' => $resource?->contents?->where('content_type', 'video')->sum('duration') ?? 0,
            ],
            'title' => $lang == 'en' ? $resource?->title : GoogleTranslateHelper::translate($resource?->title ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'order' => $resource?->order,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
