<?php

namespace App\Features\SystemManagements\Transformers;

use App\Helpers\GoogleTranslateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingCenterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        $lang = app()->getLocale();

        return [
            'id' => $resource?->id,
            'name' => $lang == 'en' ? $resource?->name : GoogleTranslateHelper::translate($resource?->name ?? '', $lang),
            'code' => $resource?->code,
            'governorate' => $this->when(
                $resource?->relationLoaded('governorate'),
                fn() => [
                    'id' => $resource->governorate?->id,
                    'name' => $lang == 'en' ? $resource->governorate?->name : GoogleTranslateHelper::translate($resource->governorate?->name ?? '', $lang),
                ]
            ),
            'governorate_id' => $resource?->governorate_id,
            'address' => $lang == 'en' ? $resource?->address : GoogleTranslateHelper::translate($resource?->address ?? '', $lang),
            'phone' => $resource?->phone,
            'email' => $resource?->email,
            'manager_name' => $resource?->manager_name,
            'capacity' => $resource?->capacity,
            'is_active' => $resource?->is_active,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
