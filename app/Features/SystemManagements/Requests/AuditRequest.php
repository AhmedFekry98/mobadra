<?php

namespace App\Features\SystemManagements\Requests;

use App\Enums\AuditAction;
use App\Enums\AuditableType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class AuditRequest
 * @package App\Features\SystemManagements\Requests
 */
class AuditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'action' => [
                'required',
                'string',
                Rule::in(AuditAction::values()),
            ],
            'auditable_type' => [
                'required',
                'string',
                'max:255',
            ],
            'auditable_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'old_values' => [
                'nullable',
                'array',
            ],
            'new_values' => [
                'nullable',
                'array',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
            'tags.*' => [
                'string',
                'max:50',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'action.required' => 'The audit action is required.',
            'action.in' => 'The selected audit action is invalid.',
            'auditable_type.required' => 'The entity type is required.',
            'auditable_id.required' => 'The entity ID is required.',
            'auditable_id.integer' => 'The entity ID must be a valid integer.',
            'auditable_id.min' => 'The entity ID must be greater than 0.',
            'description.max' => 'The description may not be greater than 1000 characters.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag may not be greater than 50 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'action' => 'audit action',
            'auditable_type' => 'entity type',
            'auditable_id' => 'entity ID',
            'old_values' => 'old values',
            'new_values' => 'new values',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert action string to enum if needed
        if ($this->has('action') && is_string($this->action)) {
            $this->merge([
                'action' => AuditAction::tryFrom($this->action)?->value ?? $this->action,
            ]);
        }

        // Ensure tags is an array
        if ($this->has('tags') && !is_array($this->tags)) {
            $this->merge([
                'tags' => is_string($this->tags) ? explode(',', $this->tags) : [],
            ]);
        }

        // Clean up tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_filter(array_map('trim', $this->tags)),
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that the auditable_type is a valid model class
            if ($this->has('auditable_type')) {
                $auditableType = AuditableType::fromModelClass($this->auditable_type);
                if (!$auditableType && !class_exists($this->auditable_type)) {
                    $validator->errors()->add('auditable_type', 'The entity type must be a valid model class.');
                }
            }

            // Validate that the entity exists if auditable_type is valid
            if ($this->has('auditable_type') && $this->has('auditable_id')) {
                $entityClass = $this->auditable_type;
                if (class_exists($entityClass)) {
                    try {
                        $entity = $entityClass::find($this->auditable_id);
                        if (!$entity) {
                            $validator->errors()->add('auditable_id', 'The specified entity does not exist.');
                        }
                    } catch (\Exception $e) {
                        $validator->errors()->add('auditable_type', 'Invalid entity type specified.');
                    }
                }
            }

            // Validate JSON fields
            $jsonFields = ['old_values', 'new_values', 'metadata'];
            foreach ($jsonFields as $field) {
                if ($this->has($field) && is_string($this->$field)) {
                    $decoded = json_decode($this->$field, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $validator->errors()->add($field, "The {$field} field must be valid JSON.");
                    }
                }
            }
        });
    }
}
