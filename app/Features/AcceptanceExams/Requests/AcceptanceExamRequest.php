<?php

namespace App\Features\AcceptanceExams\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class AcceptanceExamRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'grade_id' => ['sometimes', 'exists:grades,id'],
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'time_limit' => ['sometimes', 'nullable', 'integer', 'min:1'],
                'passing_score' => ['sometimes', 'integer', 'min:0', 'max:100'],
                'max_attempts' => ['sometimes', 'integer', 'min:1'],
                'shuffle_questions' => ['sometimes', 'boolean'],
                'show_answers' => ['sometimes', 'boolean'],
                'is_active' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'grade_id' => ['required', 'exists:grades,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['integer', 'min:0', 'max:100'],
            'max_attempts' => ['integer', 'min:1'],
            'shuffle_questions' => ['boolean'],
            'show_answers' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
