<?php

namespace App\Features\Groups\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupTeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'group_id' => $resource?->group_id,
            'teacher_id' => $resource?->teacher_id,
            'teacher' => $this->when($resource?->relationLoaded('teacher'), function () use ($resource, $lang) {
                return [
                    'id' => $resource->teacher?->id,
                    'name' => $lang == 'en' ? $resource->teacher?->name : GoogleTranslateHelper::translate($resource->teacher?->name ?? '', $lang),
                    'email' => $resource->teacher?->email,
                    'image' => $resource->teacher?->getFirstMediaUrl('user-image'),
                ];
            }),
            'assigned_at' => $resource?->assigned_at?->format('Y-m-d H:i:s'),
            'is_primary' => $resource?->is_primary,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
