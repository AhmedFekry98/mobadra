<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Requests\UpdateContentProgressRequest;
use App\Features\Groups\Services\ContentProgressService;
use App\Features\Groups\Transformers\ContentProgressResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentProgressController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected ContentProgressService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Update video progress (called from frontend while watching)
     */
    public function updateProgress(UpdateContentProgressRequest $request)
    {
        return $this->executeService(function () use ($request) {
            $userId = auth()->id();
            $data = $request->validated();

            $progress = $this->service->updateProgress(
                $userId,
                $data['lesson_content_id'],
                $data,
                $data['group_id'] ?? null
            );

            return $this->okResponse(
                ContentProgressResource::make($progress),
                "Progress updated successfully"
            );
        }, 'ContentProgressController@updateProgress');
    }

    /**
     * Get user's progress for a specific content
     */
    public function getProgress(Request $request, string $lessonContentId)
    {
        return $this->executeService(function () use ($request, $lessonContentId) {
            $userId = auth()->id();
            $groupId = $request->query('group_id');

            $progress = $this->service->getUserProgress($userId, $lessonContentId, $groupId);

            return $this->okResponse(
                $progress ? ContentProgressResource::make($progress) : null,
                "Progress retrieved successfully"
            );
        }, 'ContentProgressController@getProgress');
    }

    /**
     * Get user's progress for all contents in a group
     */
    public function getGroupProgress(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $userId = auth()->id();

            $progress = $this->service->getUserProgressByGroup($userId, $groupId);

            return $this->okResponse(
                ContentProgressResource::collection($progress),
                "Group progress retrieved successfully"
            );
        }, 'ContentProgressController@getGroupProgress');
    }

    /**
     * Mark content as completed
     */
    public function markCompleted(Request $request, string $lessonContentId)
    {
        return $this->executeService(function () use ($request, $lessonContentId) {
            $userId = auth()->id();
            $groupId = $request->input('group_id');

            $progress = $this->service->markAsCompleted($userId, $lessonContentId, $groupId);

            return $this->okResponse(
                ContentProgressResource::make($progress),
                "Content marked as completed"
            );
        }, 'ContentProgressController@markCompleted');
    }

    /**
     * Get all students progress in a group (for teachers)
     */
    public function getStudentsProgress(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $progress = $this->service->getGroupStudentsProgress($groupId);

            return $this->okResponse(
                ContentProgressResource::collection($progress),
                "Students progress retrieved successfully"
            );
        }, 'ContentProgressController@getStudentsProgress');
    }

    /**
     * Get progress summary for all students in a group (for teachers)
     */
    public function getProgressSummary(string $groupId, string $courseId)
    {
        return $this->executeService(function () use ($groupId, $courseId) {
            $summary = $this->service->getGroupProgressSummary($groupId, $courseId);

            return $this->okResponse(
                $summary,
                "Progress summary retrieved successfully"
            );
        }, 'ContentProgressController@getProgressSummary');
    }
}
