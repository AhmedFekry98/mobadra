<?php

namespace App\Features\SystemManagements\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FAQRequest
 * @package App\Features\SystemManagements\Requests
 */
class FAQRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'question' => ['required', 'string', 'max:1000'],
            'answer' => ['required', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];

        // For updates, make fields optional
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['question'][0] = 'sometimes';
            $rules['answer'][0] = 'sometimes';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question.required' => 'The question field is required.',
            'question.max' => 'The question may not be greater than 1000 characters.',
            'answer.required' => 'The answer field is required.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be at least 0.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'question' => 'question',
            'answer' => 'answer',
            'sort_order' => 'sort order',
        ];
    }
}
