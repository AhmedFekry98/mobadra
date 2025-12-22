<?php

namespace App\Features\Chat\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class SendMessageRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:text,image,file,audio,video'],
            'content' => ['required_if:type,text', 'nullable', 'string'],
            'reply_to_id' => ['nullable', 'exists:messages,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'], // 10MB max per file
        ];
    }
}
