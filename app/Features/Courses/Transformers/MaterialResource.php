<?php

namespace App\Features\Courses\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;

        return [
            'id' => $resource?->id,
            'title' => $resource?->title,
            'description' => $resource?->description,
            'file_url' => $resource?->getFirstMediaUrl('material-file'),
            'file_name' => $resource?->getFirstMedia('material-file')?->file_name,
            'file_size' => $resource?->getFirstMedia('material-file')?->size,
            'file_type' => $resource?->getFirstMedia('material-file')?->mime_type,
            'created_at' => $resource?->created_at?->toISOString(),
            'updated_at' => $resource?->updated_at?->toISOString(),
        ];
    }
}
