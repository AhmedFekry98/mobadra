<?php

namespace App\Features\Badges\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class BadgeConditionRequest extends BaseFormRequest
{
    use HandlesFailedValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'field' => ['sometimes', 'string', 'max:255'],
                'operator' => ['sometimes', 'string', 'max:255'],
                'value' => ['sometimes', 'string'],
            ];
        }
        return [
            'badge_id' => ['required', 'exists:badges,id'],
            'field' => ['required', 'string', 'max:255'],
            'operator' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string'],
        ];
    }
}
