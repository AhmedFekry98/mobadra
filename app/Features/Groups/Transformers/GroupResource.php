<?php

namespace App\Features\Groups\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'term' => [
                'id' => $resource?->course?->term?->id,
                'name' => $lang == 'en' ? $resource?->course?->term?->name : GoogleTranslateHelper::translate($resource?->course?->term?->name ?? '', $lang),
            ],
            'course' => [
                'id' => $resource?->course?->id,
                'title' => $lang == 'en' ? $resource?->course?->title : GoogleTranslateHelper::translate($resource?->course?->title ?? '', $lang),
            ],
            'grade' => [
                'id' => $resource?->grade_id,
                'name' => $lang == 'en' ? $resource?->grade?->name : GoogleTranslateHelper::translate($resource?->grade?->name ?? '', $lang),
            ],
            'governorate' => [
                'id' => $resource?->governorate_id,
                'name' => $lang == 'en' ? $resource?->governorate?->name : GoogleTranslateHelper::translate($resource?->governorate?->name ?? '', $lang),
            ],
            'name' => $lang == 'en' ? $resource?->name : GoogleTranslateHelper::translate($resource?->name ?? '', $lang),
            'max_capacity' => $resource?->max_capacity,
            'days' => $resource?->days,
            'start_date' => $resource?->start_date?->format('Y-m-d'),
            'end_date' => $resource?->end_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'location' => $lang == 'en' ? $resource?->location : GoogleTranslateHelper::translate($resource?->location ?? '', $lang),
            'location_type' => $resource?->location_type,
            'location_map_url' => $lang == 'en' ? $resource?->location_map_url : GoogleTranslateHelper::translate($resource?->location_map_url ?? '', $lang),
            'is_active' => $resource?->is_active,
            'students_count' => $resource?->relationLoaded('groupStudents')
                ? $resource->groupStudents->where('status', 'active')->count()
                : null,
            'available_slots' => $resource?->relationLoaded('groupStudents')
                ? $resource->max_capacity - $resource->groupStudents->where('status', 'active')->count()
                : null,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
