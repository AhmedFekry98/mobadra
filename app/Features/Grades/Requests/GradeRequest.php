<?php

namespace App\Features\Grades\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class GradeRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'name' => ['sometimes', 'string', 'max:255'],
                'code' => ['sometimes', 'string', 'max:50', 'unique:grades,code,' . $this->route('grade')],
                'description' => ['sometimes', 'nullable', 'string'],
                'min_age' => ['sometimes', 'nullable', 'integer', 'min:1'],
                'max_age' => ['sometimes', 'nullable', 'integer', 'min:1', 'gte:min_age'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:grades,code'],
            'description' => ['nullable', 'string'],
            'min_age' => ['nullable', 'integer', 'min:1'],
            'max_age' => ['nullable', 'integer', 'min:1', 'gte:min_age'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
