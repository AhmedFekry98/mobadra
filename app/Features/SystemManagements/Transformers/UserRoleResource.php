<?php

namespace App\Features\SystemManagements\Transformers;

use App\Features\AuthManagement\Transformers\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Features\SystemManagements\Transformers\RoleResource;

class UserRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "user" => ProfileResource::make($this->user),
            "role" => RoleResource::make($this->role),
        ];
    }
}
