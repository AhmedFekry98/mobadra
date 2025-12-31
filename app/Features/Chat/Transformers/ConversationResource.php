<?php

namespace App\Features\Chat\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $userId = auth()->id();
        $lang = app()->getLocale();

        return [
            'id' => $resource?->id,
            'type' => $resource?->type,
            'name' => $lang == 'en' ? $resource?->name : GoogleTranslateHelper::translate($resource?->name ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'is_active' => $resource?->is_active,
            'unread_count' => $resource?->unread_count ?? 0,
            'last_message_at' => $resource?->last_message_at?->toISOString(),
            'latest_message' => $this->whenLoaded('latestMessage', fn() => MessageResource::make($resource->latestMessage)),
            'participants' => $this->whenLoaded('participants', fn() => ConversationParticipantResource::collection($resource->participants)),
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }

    protected function getConversationName(int $userId): ?string
    {
        if ($this->resource->type !== 'private') {
            return null;
        }

        // For private chats, return the other participant's name
        $otherParticipant = $this->resource->participants
            ->where('user_id', '!=', $userId)
            ->first();

        return $otherParticipant?->user?->name;
    }
}
