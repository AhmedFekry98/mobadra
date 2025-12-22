<?php

namespace App\Features\Groups\Transformers;

use App\Features\Courses\Transformers\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'course_id' => $resource?->course_id,
            'course' => $this->when($resource?->relationLoaded('course'), function () use ($resource) {
                return CourseResource::make($resource->course);
            }),
            'name' => $resource?->name,
            'max_capacity' => $resource?->max_capacity,
            'days' => $resource?->days,
            'start_date' => $resource?->start_date?->format('Y-m-d'),
            'end_date' => $resource?->end_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'location' => $resource?->location,
            'location_type' => $resource?->location_type,
            'is_active' => $resource?->is_active,
            'students_count' => $this->whenLoaded('groupStudents', fn() => $resource->groupStudents->where('status', 'active')->count()),
            'available_slots' => $this->whenLoaded('groupStudents', fn() => $resource->max_capacity - $resource->groupStudents->where('status', 'active')->count()),
            'students' => $this->whenLoaded('groupStudents', fn() => GroupStudentResource::collection($resource->groupStudents)),
            'teachers' => $this->whenLoaded('groupTeachers', fn() => GroupTeacherResource::collection($resource->groupTeachers)),
            'sessions' => $this->whenLoaded('sessions', fn() => GroupSessionResource::collection($resource->sessions)),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
