<?php

namespace App\Features\AuthManagement\Transformers;

use App\Features\SystemManagements\Transformers\PermissionResource;
use App\Features\SystemManagements\Transformers\RoleResource;
use App\Features\SystemManagements\Transformers\UserInformationResource;
use App\Features\SystemManagements\Transformers\UserServiceResource;
use App\Helpers\GoogleTranslateHelper;
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
    { //  'title' => $lang == 'en' ? $resource?->course?->title : GoogleTranslateHelper::translate($resource?->course?->title ?? '', $lang),
        $resource = $this->resource;
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'name' => $lang == 'en' ? $resource?->name : GoogleTranslateHelper::translate($resource?->name ?? '', $lang),
            'email' => $resource?->email,
            'phone_verified' => (bool) $resource?->phone_verified_at,
            'email_verified' => (bool) $resource?->email_verified_at,
            'image' => $resource?->getFirstMediaUrl('user-image'),
            'role' => [
                'id' => $resource?->role?->id,
                'name' => $resource?->role?->name,
                'caption' => $lang == 'en' ? $resource?->role?->caption : GoogleTranslateHelper::translate($resource?->role?->caption ?? '', $lang),
            ],

            'user_information' => UserInformationResource::make($resource?->userInformation),
            'permissions' => $resource?->allPermissions()
                ? PermissionResource::collection($resource->allPermissions())->pluck('name')
                : collect(),
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
