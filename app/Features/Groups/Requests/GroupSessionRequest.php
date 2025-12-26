<?php

namespace App\Features\Groups\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class GroupSessionRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return $isUpdate ? $this->updateRules() : $this->createRules();
    }

    protected function createRules(): array
    {
        return [
            'session_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'topic' => ['nullable', 'string', 'max:255'],
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'meeting_provider' => ['required', 'string', 'in:zoom'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'session_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'topic' => ['nullable', 'string', 'max:255'],
            'is_cancelled' => ['sometimes', 'boolean'],
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
            'meeting_provider' => ['nullable', 'string', 'in:zoom'],
            'meeting_id' => ['nullable', 'string', 'max:255'],
            'meeting_password' => ['nullable', 'string', 'max:255'],
            'moderator_link' => ['nullable', 'string', 'url', 'max:500'],
            'attendee_link' => ['nullable', 'string', 'url', 'max:500'],
        ];
    }
}
