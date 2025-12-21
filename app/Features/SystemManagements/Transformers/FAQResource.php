<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class FAQResource
 * @package App\Features\SystemManagements\Transformers
 */
class FAQResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
      $resource = $this->resource;
      return[
        'id' => $resource?->id,
        'question' => $resource?->question,
        'answer' => $resource?->answer,
        'is_active' => $resource?->is_active,
        'sort_order' => $resource?->sort_order,
        'created_at' => $resource?->created_at->format('Y-m-d H:i:s'),
        'updated_at' => $resource?->updated_at->format('Y-m-d H:i:s'),
      ];
    }
}
