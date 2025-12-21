<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class RoleRequest extends BaseFormRequest
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
        if ($this->isMethod('put')) {
            return [
                'name' => [
                    'sometimes', 
                    'string', 
                    'max:255', 
                    'unique:roles,name,' . $this->route('id'),
                    'regex:/^[a-z_]+$/'
                ],
                'caption' => ['sometimes', 'string', 'max:255'],
                'permission_ids' => ['sometimes', 'array'],
                'permission_ids.*' => ['integer', 'exists:permissions,id'],
            ];
        }

        return [
            'name' => [
                'required', 
                'string', 
                'max:255', 
                'unique:roles,name',
                'regex:/^[a-z_]+$/'
            ],
            'caption' => ['required', 'string', 'max:255'],
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name must contain only lowercase letters and underscores (no spaces or uppercase letters).',
        ];
    }
}
