<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class StaffRequest extends BaseFormRequest
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
                        if ($role && in_array($role->name, config('staff.excluded_roles', []))) {
                            $fail('The selected role is not allowed for staff members.');
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
                'required',
                'integer',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = \App\Features\SystemManagements\Models\Role::find($value);
                    if ($role && in_array($role->name, config('staff.excluded_roles', []))) {
                        $fail('The selected role is not allowed for staff members.');
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
            'role_id.required' => 'A role must be assigned to the staff member.',
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
        // If no role_id provided and it's a create request, set default staff role
        if (!$this->has('role_id') && $this->isMethod('post')) {
            $defaultRole = \App\Features\SystemManagements\Models\Role::where('name', config('staff.default_staff_role', 'employee'))->first();
            if ($defaultRole) {
                $this->merge(['role_id' => $defaultRole->id]);
            }
        }
    }
}
