<?php

namespace App\Features\Courses\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class SubmitAnswerRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_option_id' => ['nullable', 'exists:quiz_question_options,id'],
            'text_answer' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
