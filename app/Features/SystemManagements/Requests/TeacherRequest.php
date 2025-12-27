<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class TeacherRequest extends BaseFormRequest
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
                        if ($role && $role->name !== 'teacher') {
                            $fail('The selected role must be teacher.');
                        }
                    }
                ],
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
                    if ($role && $role->name !== 'teacher') {
                        $fail('The selected role must be teacher.');
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
        // If no role_id provided, set default teacher role
        if (!$this->has('role_id')) {
            $defaultRole = \App\Features\SystemManagements\Models\Role::where('name', 'teacher')->first();
            if ($defaultRole) {
                $this->merge(['role_id' => $defaultRole->id]);
            }
        }
    }
}
