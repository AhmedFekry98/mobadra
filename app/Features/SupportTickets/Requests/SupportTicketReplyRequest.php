<?php

namespace App\Features\SupportTickets\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class SupportTicketReplyRequest extends BaseFormRequest
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
            'message' => ['required', 'string'],
            'is_internal_note' => ['sometimes', 'boolean'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'], // 10MB max per file
        ];
    }

    protected function updateRules(): array
    {
        return [
            'message' => ['sometimes', 'string'],
            'is_internal_note' => ['sometimes', 'boolean'],
        ];
    }
}
