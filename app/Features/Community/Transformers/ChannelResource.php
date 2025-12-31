<?php

namespace App\Features\Community\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'name' => $lang == 'en' ? $resource?->name : GoogleTranslateHelper::translate($resource?->name ?? '', $lang),
            'slug' => $resource?->slug,
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'type' => $resource?->type,
            'is_active' => $resource?->is_active,
            'is_private' => $resource?->is_private,
            'sort_order' => $resource?->sort_order,
            'posts_count' => $resource?->relationLoaded('posts')
                ? $resource->posts->count()
                : null,
            'channelable' => $this->when($resource?->channelable_id, function () use ($resource,$lang) {
                return [
                    'type' => $resource->channelable_type ? class_basename($resource->channelable_type) : null,
                    'id' => $resource->channelable_id,
                    'name' => $lang == 'en' ? $resource->channelable?->name : GoogleTranslateHelper::translate($resource->channelable?->name ?? '', $lang),
                ];
            }),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
