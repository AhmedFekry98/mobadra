<?php

namespace App\Features\Courses\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class CreateQuestionRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:2000'],
            'type' => ['sometimes', 'string', 'in:single_choice,multiple_choice,true_false,short_answer'],
            'points' => ['sometimes', 'integer', 'min:1'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'options' => ['required_unless:type,short_answer', 'array', 'min:2'],
            'options.*.text' => ['required', 'string', 'max:500'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
        ];
    }
}
