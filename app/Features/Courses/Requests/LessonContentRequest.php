<?php

namespace App\Features\Courses\Requests;

use App\Abstracts\BaseFormRequest;
use App\Traits\HandlesFailedValidation;

class LessonContentRequest extends BaseFormRequest
{
    use HandlesFailedValidation;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        // Get content type from request or from existing lesson content
        $contentType = $this->input('content_type');
        if (!$contentType && $isUpdate && $this->route('lesson_content')) {
            $lessonContent = \App\Features\Courses\Models\LessonContent::find($this->route('lesson_content'));
            $contentType = $lessonContent?->content_type;
        }

        $baseRules = $isUpdate ? $this->updateRules() : $this->createRules();

        // Add content_data as nullable array for updates
        if ($isUpdate) {
            $baseRules['content_data'] = ['sometimes', 'array'];
        }

        return array_merge($baseRules, $this->getContentTypeRules($contentType));
    }

    protected function createRules(): array
    {
        return [
            'lesson_id' => ['required', 'exists:lessons,id'],
            'content_type' => ['required', 'string', 'in:video,live_session,quiz,assignment,material'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'duration' => ['sometimes', 'integer', 'min:0'],
            'is_required' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function updateRules(): array
    {
        return [
            'lesson_id' => ['sometimes', 'exists:lessons,id'],
            'content_type' => ['sometimes', 'string', 'in:video,live_session,quiz,assignment,material'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'duration' => ['sometimes', 'integer', 'min:0'],
            'is_required' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function getContentTypeRules(?string $contentType): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        $required = $isUpdate ? 'sometimes' : 'required';

        return match ($contentType) {
            'video' => [
                'content_data.video_url' => [$required, 'string', 'url'],
                'content_data.video_provider' => ['sometimes', 'string', 'in:youtube,vimeo,local'],
                'content_data.duration' => ['sometimes', 'integer', 'min:0'],
                'content_data.thumbnail_url' => ['nullable', 'string'],
            ],
            'live_session' => [
                'content_data.meeting_url' => ['nullable', 'string', 'url'],
                'content_data.meeting_provider' => ['sometimes', 'string', 'in:zoom,google_meet,teams'],
                'content_data.start_time' => [$required, 'date'],
                'content_data.end_time' => ['nullable', 'date', 'after:content_data.start_time'],
                'content_data.max_participants' => ['nullable', 'integer', 'min:1'],
            ],
            'quiz' => [
                'content_data.time_limit' => ['nullable', 'integer', 'min:1'],
                'content_data.passing_score' => ['sometimes', 'integer', 'min:0', 'max:100'],
                'content_data.max_attempts' => ['sometimes', 'integer', 'min:1'],
                'content_data.shuffle_questions' => ['sometimes', 'boolean'],
                'content_data.show_answers' => ['sometimes', 'boolean'],
            ],
            'assignment' => [
                'content_data.instructions' => ['nullable', 'string'],
                'content_data.due_date' => ['nullable', 'date'],
                'content_data.max_score' => ['sometimes', 'integer', 'min:0'],
                'content_data.allow_late_submission' => ['sometimes', 'boolean'],
                'content_data.allowed_file_types' => ['nullable', 'array'],
                'content_data.max_file_size' => ['nullable', 'integer', 'min:1'],
            ],
            'material' => [
                'content_data.file_url' => [$required, 'string'],
                'content_data.file_type' => [$required, 'string'],
                'content_data.file_size' => ['nullable', 'integer', 'min:0'],
                'content_data.is_downloadable' => ['sometimes', 'boolean'],
            ],
            default => [],
        };
    }
}
