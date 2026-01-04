<?php

namespace App\Features\Competitions\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class CompetitionLevelRequest extends BaseFormRequest
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
            'competition_id' => ['required', 'exists:competitions,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_order' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level_order' => ['sometimes', 'integer', 'min:1'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
