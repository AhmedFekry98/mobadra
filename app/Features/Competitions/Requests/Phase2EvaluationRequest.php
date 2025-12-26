<?php

namespace App\Features\Competitions\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class Phase2EvaluationRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idea_clarity' => ['required', 'numeric', 'min:0', 'max:25'],
            'technical_understanding' => ['required', 'numeric', 'min:0', 'max:25'],
            'logic_analysis' => ['required', 'numeric', 'min:0', 'max:25'],
            'presentation_communication' => ['required', 'numeric', 'min:0', 'max:25'],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
