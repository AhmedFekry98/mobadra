<?php

namespace App\Features\AcceptanceExams\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class AcceptanceExamQuestionRequest extends BaseFormRequest
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
                'question' => ['sometimes', 'string'],
                'type' => ['sometimes', 'in:single_choice,multiple_choice,true_false,short_answer'],
                'points' => ['sometimes', 'integer', 'min:1'],
                'order' => ['sometimes', 'integer', 'min:0'],
                'explanation' => ['sometimes', 'nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
                'options' => ['sometimes', 'array'],
                'options.*.text' => ['required_with:options', 'string'],
                'options.*.is_correct' => ['sometimes', 'boolean'],
            ];
        }

        return [
            'question' => ['required', 'string'],
            'type' => ['in:single_choice,multiple_choice,true_false,short_answer'],
            'points' => ['integer', 'min:1'],
            'order' => ['integer', 'min:0'],
            'explanation' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'options' => ['array'],
            'options.*.text' => ['required_with:options', 'string'],
            'options.*.is_correct' => ['sometimes', 'boolean'],
        ];
    }
}
