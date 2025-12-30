<?php

namespace App\Features\Courses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoQuizAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:video_quiz_questions,id',
            'selected_option_id' => 'required|exists:video_quiz_options,id',
        ];
    }
}
