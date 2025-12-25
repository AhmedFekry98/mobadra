<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        $media = $resource?->getFirstMedia('material_file');

        return [
            'id' => $resource?->id,
            'file_url' => $media?->getUrl(),
            'file_name' => $media?->file_name,
            'file_size' => $media?->size,
            'file_type' => $media?->mime_type ?? $resource?->file_type,
            'is_downloadable' => $resource?->is_downloadable,
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
