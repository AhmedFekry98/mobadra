<?php

namespace App\Features\Groups\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class BulkAttendanceRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'exists:users,id'],
            'attendances.*.status' => ['required', 'string', 'in:present,absent,late,excused'],
            'attendances.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
