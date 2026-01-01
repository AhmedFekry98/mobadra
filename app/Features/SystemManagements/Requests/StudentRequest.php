<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class StudentRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
                'role_id' => [
                    'sometimes',
                    'integer',
                    'exists:roles,id',
                    function ($attribute, $value, $fail) {
                        $role = \App\Features\SystemManagements\Models\Role::find($value);
                        if ($role && $role->name !== 'student') {
                            $fail('The selected role must be student.');
                        }
                    }
                ],
                // User Information fields
                'user_information' => ['sometimes', 'array'],
                'user_information.governorate_id' => ['sometimes', 'nullable', 'exists:governorates,id'],
                'user_information.phone_code' => ['sometimes', 'nullable', 'string', 'max:10'],
                'user_information.phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
                'user_information.date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
                'user_information.grade_id' => ['sometimes', 'nullable', 'exists:grades,id'],
                'user_information.gender' => ['sometimes', 'nullable', 'string'],
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
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => [
                'sometimes',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = \App\Features\SystemManagements\Models\Role::find($value);
                    if ($role && $role->name !== 'student') {
                        $fail('The selected role must be student.');
                    }
                }
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'role_id.exists' => 'The selected role does not exist.',
            'password.confirmed' => 'The password confirmation does not match.',
            'email.unique' => 'This email address is already registered.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If no role_id provided, set default student role
        if (!$this->has('role_id')) {
            $defaultRole = \App\Features\SystemManagements\Models\Role::where('name', 'student')->first();
            if ($defaultRole) {
                $this->merge(['role_id' => $defaultRole->id]);
            }
        }
    }
}
