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
            'data' => $this->collection,
        ];
    }
}
