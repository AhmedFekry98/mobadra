<?php

namespace App\Features\AuthManagement\Requests;

use App\Abstracts\BaseFormRequest;
use App\Enums\Gender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Handle user_services if it's sent as a JSON string
        if ($this->has('user_services') && is_string($this->input('user_services'))) {
            $decoded = json_decode($this->input('user_services'), true);
            $this->merge([
                'user_services' => is_array($decoded) ? $decoded : []
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic user info
            'name' => ['sometimes', 'string', 'max:255'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],

            // User Information fields
            'user_information' => ['sometimes', 'array'],
            'user_information.governorate_id' => ['sometimes', 'nullable', 'exists:governorates,id'],
            'user_information.phone_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'user_information.phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'user_information.date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'user_information.grade_id' => ['sometimes', 'nullable', 'exists:grades,id'],
            'user_information.gender' => ['sometimes', 'nullable', Rule::in(Gender::values())],
            'user_information.nationality' => ['sometimes', 'nullable', 'string', 'max:100'],
            'user_information.address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'user_information.city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'user_information.state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'user_information.country' => ['sometimes', 'nullable', 'string', 'max:100'],
            'user_information.postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'user_information.emergency_contact_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'user_information.emergency_contact_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'user_information.bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'user_information.social_links' => ['sometimes', 'nullable', 'array'],
            'user_information.social_links.facebook' => ['sometimes', 'nullable', 'url'],
            'user_information.social_links.twitter' => ['sometimes', 'nullable', 'url'],
            'user_information.social_links.linkedin' => ['sometimes', 'nullable', 'url'],
            'user_information.social_links.instagram' => ['sometimes', 'nullable', 'url'],
            'user_information.social_links.website' => ['sometimes', 'nullable', 'url'],

            // User Services
            'user_services' => ['nullable', 'array'],
            'user_services.*' => ['sometimes', 'exists:service_types,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'image' => 'profile image',
            'user_information.phone_code' => 'phone code',
            'user_information.phone_number' => 'phone number',
            'user_information.date_of_birth' => 'date of birth',
            'user_information.emergency_contact_name' => 'emergency contact name',
            'user_information.emergency_contact_phone' => 'emergency contact phone',
            'user_information.social_links.facebook' => 'Facebook URL',
            'user_information.social_links.twitter' => 'Twitter URL',
            'user_information.social_links.linkedin' => 'LinkedIn URL',
            'user_information.social_links.instagram' => 'Instagram URL',
            'user_information.social_links.website' => 'website URL',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'image.image' => 'The file must be a valid image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
            'image.max' => 'The image must not be greater than 5MB.',
            'user_information.date_of_birth.before' => 'Date of birth must be before today.',
            'user_services.*.exists' => 'Selected service does not exist.',
        ];
    }
}
