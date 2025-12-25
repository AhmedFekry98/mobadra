<?php

namespace App\Features\Groups\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'term' => [
                'id' => $resource?->course?->term?->id,
                'name' => $resource?->course?->term?->name,
            ],
            'course' => [
                'id' => $resource?->course?->id,
                'title' => $resource?->course?->title,
            ],
            'grade' => [
                'id' => $resource?->grade_id,
                'name' => $resource?->grade?->name,
            ],
            'name' => $resource?->name,
            'max_capacity' => $resource?->max_capacity,
            'days' => $resource?->days,
            'start_date' => $resource?->start_date?->format('Y-m-d'),
            'end_date' => $resource?->end_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'location' => $resource?->location,
            'location_type' => $resource?->location_type,
            'location_map_url' => $resource?->location_map_url,
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
