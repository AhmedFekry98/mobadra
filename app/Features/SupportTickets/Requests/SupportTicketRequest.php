<?php

namespace App\Features\SupportTickets\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class SupportTicketRequest extends BaseFormRequest
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
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'category' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'subject' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,urgent'],
            'status' => ['sometimes', 'string', 'in:open,in_progress,waiting_reply,resolved,closed'],
            'category' => ['nullable', 'string', 'max:100'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
