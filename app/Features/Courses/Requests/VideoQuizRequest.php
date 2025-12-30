<?php

namespace App\Features\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'max_questions' => 'sometimes|integer|min:1|max:10',
            'passing_score' => 'sometimes|integer|min:0|max:100',
            'is_required' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
