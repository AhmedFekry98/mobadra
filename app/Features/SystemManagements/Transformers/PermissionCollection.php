<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $group = $request->get('group');

        // If group=true, return grouped permissions
        if ($group === 'true') {
            return $this->getGroupedResponse($request);
        }

        // Regular response
        return [
            'per_page' => $this->collection->count(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'items' => PermissionResource::collection($this->collection),
        ];
    }

    /**
     * Get grouped permissions response
     */
    private function getGroupedResponse(Request $request): array
    {
        $grouped = $this->collection->groupBy('group')->map(function ($groupPermissions, $groupName) use ($request) {
            return [
                'name' => $groupName,
                'permissions' => PermissionResource::collection($groupPermissions)->resolve($request),
            ];
        })->values();

        $isPaginated = $request->has('page');

        if ($isPaginated) {
            return [
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'next_page_url' => $this->nextPageUrl(),
                'total' => $this->total(),
                'items' => $grouped,
            ];
        }

        return $grouped->toArray();
    }
}
