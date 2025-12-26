<?php

namespace App\Features\Groups\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class UpdateContentProgressRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_content_id' => ['required', 'exists:lesson_contents,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'last_position' => ['required', 'integer', 'min:0'], // بالثواني
            'watch_time' => ['sometimes', 'integer', 'min:0'], // الوقت المضاف بالثواني
        ];
    }
}
