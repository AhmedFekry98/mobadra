<?php

namespace App\Features\Groups\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class AttendanceRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'exists:users,id'],
            'status' => ['required', 'string', 'in:present,absent,late,excused'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
