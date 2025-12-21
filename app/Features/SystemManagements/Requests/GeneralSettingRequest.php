<?php

namespace App\Features\SystemManagements\Requests;

use App\Abstracts\BaseFormRequest;


class GeneralSettingRequest extends BaseFormRequest
{
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
        $settingId = $this->route('general_setting');

        return match ($this->method()) {
            'POST' => $this->createRules(),
            'PUT' => $this->updateRules($settingId),
            'PATCH' => $this->patchRules($settingId),
            default => []
        };
    }

    /**
     * Get validation rules for creating a new setting
     */
    private function createRules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:general_settings,key'],
            'value' => ['required', 'string'],
            'caption' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    /**
     * Get validation rules for updating a setting (PUT)
     */
    private function updateRules($settingId): array
    {
        return [
            // 'key' => ['required', 'string', 'max:255', 'unique:general_settings,key,' . $settingId],
            'value' => ['required', 'string'],
            'caption' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    /**
     * Get validation rules for partial update (PATCH)
     */
    private function patchRules($settingId): array
    {
        return [
            // 'key' => ['sometimes', 'string', 'max:255', 'unique:general_settings,key,' . $settingId],
            'value' => ['sometimes', 'string'],
            'caption' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'key.required' => 'The setting key is required.',
            'key.unique' => 'A setting with this key already exists.',
            'key.max' => 'The setting key may not be greater than 255 characters.',
            'value.required' => 'The setting value is required.',
            'caption.required' => 'The setting caption is required.',
            'description.required' => 'The setting description is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'key' => 'setting key',
            'value' => 'setting value',
            'caption' => 'setting caption',
            'description' => 'setting description',
        ];
    }
}
