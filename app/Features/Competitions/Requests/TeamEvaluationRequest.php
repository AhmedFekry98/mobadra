<?php

namespace App\Features\Competitions\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class TeamEvaluationRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idea_strength' => ['required', 'numeric', 'min:0', 'max:40'],
            'implementation' => ['required', 'numeric', 'min:0', 'max:40'],
            'teamwork' => ['required', 'numeric', 'min:0', 'max:30'],
            'problem_solving' => ['required', 'numeric', 'min:0', 'max:40'],
            'final_presentation' => ['required', 'numeric', 'min:0', 'max:50'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
