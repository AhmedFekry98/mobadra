<?php

namespace App\Features\Courses\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class CourseRequest extends BaseFormRequest
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
                'term_id' => ['sometimes', 'exists:terms,id'],
                'grade_id' => ['sometimes','exists:grades,id'],
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'slug' => ['sometimes', 'string', 'max:255', 'unique:courses,slug,' . $this->route('course')],
                'is_active' => ['sometimes', 'boolean'],
                'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ];
        }

        return [
            'term_id' => ['required', 'exists:terms,id'],
            'grade_id' => ['required','exists:grades,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', 'unique:courses,slug'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
