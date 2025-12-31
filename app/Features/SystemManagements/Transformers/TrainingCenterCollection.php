<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TrainingCenterCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'per_page' => $this->collection->count(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'items' => TrainingCenterResource::collection($this->collection),
        ];
    }
}
