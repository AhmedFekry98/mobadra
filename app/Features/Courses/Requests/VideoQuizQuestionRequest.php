<?php

namespace App\Features\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => 'required|string|max:1000',
            'type' => 'sometimes|in:single_choice,true_false',
            'points' => 'sometimes|integer|min:1|max:10',
            'order' => 'sometimes|integer|min:0',
            'timestamp_seconds' => 'nullable|integer|min:0',
            'explanation' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean',
            'options' => 'required|array|min:2|max:6',
            'options.*.option_text' => 'required|string|max:500',
            'options.*.is_correct' => 'required|boolean',
            'options.*.order' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'options.required' => 'At least 2 options are required',
            'options.min' => 'At least 2 options are required',
            'options.*.option_text.required' => 'Option text is required',
            'options.*.is_correct.required' => 'Please specify if option is correct',
        ];
    }
}
