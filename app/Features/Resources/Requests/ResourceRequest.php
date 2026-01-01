<?php

namespace App\Features\Resources\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class ResourceRequest extends BaseFormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'in:video,file,document,image,audio'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'file' => ['required', 'file', 'max:512000'], // 500MB max
            'thumbnail' => ['nullable', 'image', 'max:5120'], // 5MB max
            'is_downloadable' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['sometimes', 'string', 'in:video,file,document,image,audio'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'file' => ['sometimes', 'file', 'max:512000'],
            'thumbnail' => ['nullable', 'image', 'max:5120'],
            'is_downloadable' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
