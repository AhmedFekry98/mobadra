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
            'grade_id' => ['required', 'exists:grades,id'],
            'governorate_id' => ['required_if:location_type,offline', 'nullable', 'exists:governorates,id'],
            'name' => ['required', 'string', 'max:255'],
            'max_capacity' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location_type' => ['sometimes', 'string', 'in:online,offline'],
            'location' => ['required_if:location_type,offline','nullable', 'string', 'max:255'],
            'location_map_url' => ['required_if:location_type,offline','nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'course_id' => ['sometimes', 'exists:courses,id'],
            'grade_id' => ['sometimes', 'exists:grades,id'],
            'governorate_id' => ['required_if:location_type,offline', 'nullable', 'exists:governorates,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'max_capacity' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'days' => ['sometimes', 'array', 'min:1'],
            'days.*' => ['string', 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'location_type' => ['sometimes', 'string', 'in:online,offline'],
            'location_map_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
