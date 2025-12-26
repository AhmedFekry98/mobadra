<?php

namespace App\Features\Competitions\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class CompetitionTeamRequest extends BaseFormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'track' => ['required', 'in:online,offline'],
            'lab' => ['nullable', 'string', 'max:255'],
            'governorate' => ['required', 'string', 'max:100'],
            'member_ids' => ['sometimes', 'array'],
            'member_ids.*' => ['exists:competition_participants,id'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'track' => ['sometimes', 'in:online,offline'],
            'lab' => ['nullable', 'string', 'max:255'],
            'governorate' => ['sometimes', 'string', 'max:100'],
            'project_title' => ['nullable', 'string', 'max:255'],
            'project_description' => ['nullable', 'string'],
        ];
    }
}
