<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        $files = $resource?->getMedia('assignment_files')->map(function ($media) {
            return [
                'id' => $media->id,
                'file_url' => $media->getUrl(),
                'file_name' => $media->file_name,
                'file_size' => $media->size,
                'file_type' => $media->mime_type,
            ];
        });

        return [
            'id' => $resource?->id,
            'instructions' => $lang == 'en' ? $resource?->instructions : GoogleTranslateHelper::translate($resource?->instructions ?? '', $lang),
            'due_date' => $resource?->due_date?->toISOString(),
            'max_score' => $resource?->max_score,
            'allow_late_submission' => $resource?->allow_late_submission,
            'allowed_file_types' => $resource?->allowed_file_types,
            'max_file_size' => $resource?->max_file_size,
            'is_overdue' => $resource?->isOverdue(),
            'files' => $files,
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
