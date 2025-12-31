<?php

namespace App\Features\Groups\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'group_id' => $resource?->group_id,
            'session_id' => $resource?->session_id,
            'student_id' => $resource?->student_id,
            'student' => [
                    'id' => $resource->student?->id,
                    'name' => $lang == 'en' ? $resource->student?->name : GoogleTranslateHelper::translate($resource->student?->name ?? '', $lang),
                    'email' => $resource->student?->email,
                    'image' => $resource->student?->getFirstMediaUrl('user-image'),
            ],
            'status' => $resource?->status,
            'attended_at' => $resource?->attended_at?->format('Y-m-d H:i:s'),
            'notes' => $resource?->notes,
            'recorded_by' => $resource?->recorded_by,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
