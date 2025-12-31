<?php

namespace App\Features\Courses\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'assignment_id' => $resource?->assignment_id,
            'content' => $lang == 'en' ? $resource?->content : GoogleTranslateHelper::translate($resource?->content ?? '', $lang),
            'status' => $resource?->status,
            'submitted_at' => $resource?->submitted_at?->toISOString(),
            'score' => $resource?->score,
            'feedback' => $lang == 'en' ? $resource?->feedback : GoogleTranslateHelper::translate($resource?->feedback ?? '', $lang),
            'graded_at' => $resource?->graded_at?->toISOString(),
            'is_late' => $resource?->is_late,
            'student' => [
                'id' => $resource->student?->id,
                'name' => $resource->student?->name,
            ],
            'grader' => [
                'id' => $resource->grader?->id,
                'name' => $resource->grader?->name,
            ],
            'attachments' => $resource?->getMedia('attachments')->map(fn($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'extension' => $media->extension,
            ]),
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
