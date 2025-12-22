<?php

namespace App\Features\Groups\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupStudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'group_id' => $resource?->group_id,
            'student_id' => $resource?->student_id,
            'student' => $this->when($resource?->relationLoaded('student'), function () use ($resource) {
                return [
                    'id' => $resource->student?->id,
                    'name' => $resource->student?->name,
                    'email' => $resource->student?->email,
                    'image' => $resource->student?->getFirstMediaUrl('user-image'),
                ];
            }),
            'enrolled_at' => $resource?->enrolled_at?->format('Y-m-d H:i:s'),
            'status' => $resource?->status,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
