<?php

namespace App\Features\AuthManagement\Transformers;

use App\Features\SystemManagements\Transformers\PermissionResource;
use App\Features\SystemManagements\Transformers\RoleResource;
use App\Features\SystemManagements\Transformers\UserInformationResource;
use App\Features\SystemManagements\Transformers\UserServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProfileResource extends JsonResource
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
            'name' => $resource?->name,
            'email' => $resource?->email,
            'image' => $resource?->getFirstMediaUrl('user-image'),
            'role' => RoleResource::make($resource?->role) ?? 'admin',
            'user_information' => UserInformationResource::make($resource?->userInformation),
            'permissions' => $resource?->allPermissions()
                ? PermissionResource::collection($resource->allPermissions())->pluck('name')
                : collect(),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
