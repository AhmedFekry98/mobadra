<?php

namespace App\Features\Resources\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();

        return [
            'id' => $resource?->id,
            'title' => $lang == 'en' ? $resource?->title : GoogleTranslateHelper::translate($resource?->title ?? '', $lang),
            'description' => $lang == 'en' ? $resource?->description : GoogleTranslateHelper::translate($resource?->description ?? '', $lang),
            'type' => $resource?->type,
            'grade' => $resource?->grade ? [
                'id' => $resource->grade->id,
                'name' => $lang == 'en' ? $resource->grade->name : GoogleTranslateHelper::translate($resource->grade->name ?? '', $lang),
            ] : null,
            'uploader' => $resource?->uploader ? [
                'id' => $resource->uploader->id,
                'name' => $resource->uploader->name,
            ] : null,
            'file_url' => $resource?->getFirstMediaUrl('resource_file'),
            'file_name' => $resource?->getFirstMedia('resource_file')?->file_name,
            'file_size' => $resource?->getFirstMedia('resource_file')?->size,
            'file_mime_type' => $resource?->getFirstMedia('resource_file')?->mime_type,
            'thumbnail_url' => $resource?->getFirstMediaUrl('resource_thumbnail'),
            'is_downloadable' => $resource?->is_downloadable,
            'is_active' => $resource?->is_active,
            'download_count' => $resource?->download_count,
            'view_count' => $resource?->view_count,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
