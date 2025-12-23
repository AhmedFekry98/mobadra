<?php

namespace App\Features\SupportTickets\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SupportTicketCollection extends ResourceCollection
{
    public $collects = SupportTicketResource::class;

    public function toArray(Request $request): array
    {
        return [
            'per_page' => $this->collection->count(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'items' => SupportTicketResource::collection($this->collection),
        ];
    }
}
