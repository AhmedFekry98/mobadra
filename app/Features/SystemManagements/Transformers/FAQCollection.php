<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class FAQCollection
 * @package App\Features\SystemManagements\Transformers
 */
class FAQCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     */
    public $collects = FAQResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'per_page' => $this->collection->count(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'items' => FAQResource::collection($this->collection),
        ];
    }

}
