<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class PermissionRequest extends BaseFormRequest
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
                'name' => ['sometimes', 'string', 'max:255', 'unique:permissions,name'],
                'caption' => ['sometimes', 'string', 'max:255'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'caption' => ['required', 'string', 'max:255'],
        ];
    }
}
