<?php

namespace App\Features\SystemManagements\Transformers;

use App\Features\Grades\Transformers\GradeResource;
use App\Helpers\GoogleTranslateHelper;
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
        $lang = app()->getLocale();
        return [
            'id' => $resource?->id,
            'phone_code' => $resource?->phone_code,
            'phone_number' => $resource?->phone_number,
            'date_of_birth' => $resource?->date_of_birth ?
                (is_string($resource->date_of_birth) ? $resource->date_of_birth : $resource->date_of_birth->format('Y-m-d'))
                : null,
            'gender' => $resource?->gender,
            'grade'=> [
                'id' => $resource?->grade?->id,
                'name' => $lang == 'en' ? $resource?->grade?->name : GoogleTranslateHelper::translate($resource?->grade?->name ?? '', $lang),
            ],
            'governorate'=> [
                'id' => $resource?->governorate?->id,
                'name' => $lang == 'en' ? $resource?->governorate?->name : GoogleTranslateHelper::translate($resource?->governorate?->name ?? '', $lang),
            ],
            'acceptance_exam' => $resource?->acceptance_exam,
            'nationality' => $lang == 'en' ? $resource?->nationality : GoogleTranslateHelper::translate($resource?->nationality ?? '', $lang),
            'address' => $lang == 'en' ? $resource?->address : GoogleTranslateHelper::translate($resource?->address ?? '', $lang),
            'city' => $lang == 'en' ? $resource?->city : GoogleTranslateHelper::translate($resource?->city ?? '', $lang),
            'state' => $lang == 'en' ? $resource?->state : GoogleTranslateHelper::translate($resource?->state ?? '', $lang),
            'country' => $lang == 'en' ? $resource?->country : GoogleTranslateHelper::translate($resource?->country ?? '', $lang),
            'postal_code' => $resource?->postal_code,
            'emergency_contact_name' => $lang == 'en' ? $resource?->emergency_contact_name : GoogleTranslateHelper::translate($resource?->emergency_contact_name ?? '', $lang),
            'emergency_contact_phone' => $resource?->emergency_contact_phone,
            'bio' => $lang == 'en' ? $resource?->bio : GoogleTranslateHelper::translate($resource?->bio ?? '', $lang),
            'social_links' => $resource?->social_links,
            'created_at' => $resource?->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resource?->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
