<?php

namespace App\Features\Groups\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class GroupRequest extends BaseFormRequest
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
            'course_id' => ['required', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'max_capacity' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'location_type' => ['sometimes', 'string', 'in:online,physical'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'course_id' => ['sometimes', 'exists:courses,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'max_capacity' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'days' => ['sometimes', 'array', 'min:1'],
            'days.*' => ['string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'location_type' => ['sometimes', 'string', 'in:online,physical'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
