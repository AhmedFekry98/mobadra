<?php

namespace App\Features\AcceptanceExams\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class AcceptanceExamAnswerRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_option_id' => ['nullable', 'exists:acceptance_exam_question_options,id'],
            'text_answer' => ['nullable', 'string'],
        ];
    }
}
