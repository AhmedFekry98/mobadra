<?php

namespace App\Features\Chat\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class CreateConversationRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:private,group,support'],
            'participant_id' => ['required_if:type,private', 'exists:users,id'],
            'group_id' => ['required_if:type,group', 'exists:groups,id'],
        ];
    }
}
