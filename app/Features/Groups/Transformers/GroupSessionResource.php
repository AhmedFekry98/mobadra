<?php

namespace App\Features\Groups\Transformers;

use App\Features\Groups\Models\ContentProgress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'group' =>[
                'id' => $resource?->group?->id,
                'name' => $resource?->group?->name,
                'location_type' => $resource?->group?->location_type,
                'location' => $resource?->group?->location,
                'location_map_url' => $resource?->group?->location_map_url,
            ],
            "course" => [
                "id" => $resource?->lesson?->course?->id,
                "title" => $resource?->lesson?->course?->title,
            ],
            "term" => [
                "id" => $resource?->lesson?->course?->term?->id,
                "name" => $resource?->lesson?->course?->term?->name,
            ],
            'session_date' => $resource?->session_date?->format('Y-m-d'),
            'start_time' => $resource?->start_time,
            'end_time' => $resource?->end_time,
            'topic' => $resource?->topic,
            'lesson' => [
                'id' => $resource?->lesson?->id,
                'title' => $resource?->lesson?->title,
            ],
            'is_cancelled' => $resource?->is_cancelled,
            'cancellation_reason' => $resource?->cancellation_reason,
            'meeting_provider' => $resource?->meeting_provider,
            'meeting_id' => $resource?->meeting_id,
            'link_meeing' => auth()->user()->role_name == 'student' ? $resource?->attendee_link : $resource?->moderator_link,
            // 'recording_url' => $resource?->recording_url,
            // 'recording_download_url' => $resource?->recording_download_url,
            // 'recording_password' => $resource?->recording_password,
            // 'has_meeting' => !empty($resource?->moderator_link) || !empty($resource?->attendee_link),
            'content_progress' => $this->getContentProgress($resource),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    protected function getContentProgress($resource): array
    {
        if (!$resource?->lesson_id || !auth()->check()) {
            return [];
        }

        $userId = auth()->id();
        $groupId = $resource->group_id;
        $lessonContents = $resource->lesson?->contents ?? collect();

        return $lessonContents->map(function ($content) use ($userId, $groupId) {
            // Check for progress with matching group_id OR null group_id
            $progress = ContentProgress::where('user_id', $userId)
                ->where('lesson_content_id', $content->id)
                ->where(function ($query) use ($groupId) {
                    $query->where('group_id', $groupId)
                        ->orWhereNull('group_id');
                })
                ->first();

            return [
                'lesson_content_id' => $content->id,
                'title' => $content->title,
                'content_type' => $content->content_type,
                'progress_percentage' => $progress?->progress_percentage ?? 0,
                'is_completed' => $progress?->is_completed ?? false,
                'last_position' => $progress?->last_position ?? 0,
                'watch_time' => $progress?->watch_time ?? 0,
            ];
        })->toArray();
    }
}
