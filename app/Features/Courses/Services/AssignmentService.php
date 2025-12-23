<?php

namespace App\Features\Courses\Services;

use App\Features\Courses\Models\Assignment;
use App\Features\Courses\Models\AssignmentSubmission;
use Illuminate\Pagination\LengthAwarePaginator;

class AssignmentService
{
    public function getAssignmentById(int $id): Assignment
    {
        return Assignment::findOrFail($id);
    }

    public function createSubmission(int $assignmentId, int $studentId, array $data): AssignmentSubmission
    {
        $assignment = Assignment::findOrFail($assignmentId);

        // Check if already submitted
        $existing = $assignment->getStudentSubmission($studentId);
        if ($existing && $existing->status === 'submitted') {
            throw new \Exception('You have already submitted this assignment');
        }

        // Check if overdue and late submission not allowed
        if ($assignment->isOverdue() && !$assignment->allow_late_submission) {
            throw new \Exception('This assignment is overdue and late submissions are not allowed');
        }

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
            ],
            [
                'content' => $data['content'] ?? null,
                'status' => 'draft',
            ]
        );

        // Handle attachments
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $attachment) {
                $submission->addMedia($attachment)->toMediaCollection('attachments');
            }
        }

        return $submission->load('media');
    }

    public function submitAssignment(int $submissionId): AssignmentSubmission
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        if ($submission->status === 'submitted') {
            throw new \Exception('This assignment has already been submitted');
        }

        $submission->submit();

        return $submission;
    }

    public function gradeSubmission(int $submissionId, int $graderId, array $data): AssignmentSubmission
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        if ($submission->status !== 'submitted') {
            throw new \Exception('This assignment has not been submitted yet');
        }

        $submission->grade(
            $data['score'],
            $data['feedback'] ?? '',
            $graderId
        );

        return $submission->load(['student', 'grader']);
    }

    public function getAssignmentSubmissions(int $assignmentId): LengthAwarePaginator
    {
        return AssignmentSubmission::with(['student', 'grader', 'media'])
            ->where('assignment_id', $assignmentId)
            ->orderByDesc('submitted_at')
            ->paginate(20);
    }

    public function getStudentSubmission(int $assignmentId, int $studentId): ?AssignmentSubmission
    {
        return AssignmentSubmission::with(['media'])
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function getStudentAssignments(int $studentId): LengthAwarePaginator
    {
        return AssignmentSubmission::with(['assignment.lessonContent.lesson', 'media'])
            ->where('student_id', $studentId)
            ->orderByDesc('created_at')
            ->paginate(20);
    }
}
