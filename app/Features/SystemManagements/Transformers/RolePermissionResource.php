<?php

namespace App\Features\SystemManagements\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\SystemManagements\Transformers\RoleResource;
use App\Features\SystemManagements\Transformers\PermissionResource;

class RolePermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "role" => RoleResource::make($this->role),
            "permission" => PermissionResource::make($this->permission),
        ];
    }
}
