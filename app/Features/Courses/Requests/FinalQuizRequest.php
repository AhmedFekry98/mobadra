<?php

namespace App\Features\Courses\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class FinalQuizRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:1'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'show_answers' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
