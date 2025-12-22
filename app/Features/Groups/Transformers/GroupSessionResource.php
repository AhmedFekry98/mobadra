<?php

namespace App\Features\Groups\Transformers;

use App\Features\Courses\Transformers\LessonContentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'group_id' => $resource?->group_id,
            'group' => $this->when($resource?->relationLoaded('group'), function () use ($resource) {
                return GroupResource::make($resource->group);
            }),
            'session_date' => $resource?->session_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'topic' => $resource?->topic,
            'lesson_content_id' => $resource?->lesson_content_id,
            'lesson_content' => $this->when($resource?->relationLoaded('lessonContent'), function () use ($resource) {
                return LessonContentResource::make($resource->lessonContent);
            }),
            'is_cancelled' => $resource?->is_cancelled,
            'cancellation_reason' => $resource?->cancellation_reason,
            'meeting_provider' => $resource?->meeting_provider,
            'meeting_id' => $resource?->meeting_id,
            'has_meeting' => !empty($resource?->moderator_link) || !empty($resource?->attendee_link),
            'attendances' => $this->whenLoaded('attendances', fn() => AttendanceResource::collection($resource->attendances)),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
