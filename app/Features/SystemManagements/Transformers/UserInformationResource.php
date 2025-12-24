<?php

namespace App\Features\SystemManagements\Transformers;

use App\Features\Grades\Transformers\GradeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'phone_code' => $resource?->phone_code,
            'phone_number' => $resource?->phone_number,
            'date_of_birth' => $resource?->date_of_birth ?
                (is_string($resource->date_of_birth) ? $resource->date_of_birth : $resource->date_of_birth->format('Y-m-d'))
                : null,
            'gender' => $resource?->gender,
            'grade'=> GradeResource::make($resource?->grade),
            'nationality' => $resource?->nationality,
            'address' => $resource?->address,
            'city' => $resource?->city,
            'state' => $resource?->state,
            'country' => $resource?->country,
            'postal_code' => $resource?->postal_code,
            'emergency_contact_name' => $resource?->emergency_contact_name,
            'emergency_contact_phone' => $resource?->emergency_contact_phone,
            'bio' => $resource?->bio,
            'social_links' => $resource?->social_links,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
