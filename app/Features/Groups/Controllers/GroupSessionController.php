<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Requests\GroupSessionRequest;
use App\Features\Groups\Services\GroupSessionService;
use App\Features\Groups\Transformers\GroupSessionResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class GroupSessionController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected GroupSessionService $service
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $this->authorize('viewAny', Group::class);

            $sessions = $this->service->getSessionsByGroup($groupId);

            return $this->okResponse(
                GroupSessionResource::collection($sessions),
                "Sessions retrieved successfully"
            );
        }, 'GroupSessionController@index');
    }

    public function store(GroupSessionRequest $request, string $groupId)
    {
        return $this->executeService(function () use ($request, $groupId) {
            $this->authorize('create', Group::class);

            $data = $request->validated();
            $data['group_id'] = $groupId;

            $session = $this->service->storeSession($data);

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session created successfully"
            );
        }, 'GroupSessionController@store');
    }

    public function show(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('view', Group::find($session->group_id));

            return $this->okResponse(
                GroupSessionResource::make($session->load(['group', 'lessonContent', 'attendances.student'])),
                "Session retrieved successfully"
            );
        }, 'GroupSessionController@show');
    }

    public function update(GroupSessionRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('update', Group::find($session->group_id));

            $session = $this->service->updateSession($id, $request->validated());

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session updated successfully"
            );
        }, 'GroupSessionController@update');
    }

    public function destroy(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('delete', Group::find($session->group_id));

            $this->service->deleteSession($id);

            return $this->okResponse(
                null,
                "Session deleted successfully"
            );
        }, 'GroupSessionController@destroy');
    }

    public function cancel(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $this->authorize('update', Group::find($session->group_id));

            $session = $this->service->cancelSession($id, request('reason'));

            return $this->okResponse(
                GroupSessionResource::make($session),
                "Session cancelled successfully"
            );
        }, 'GroupSessionController@cancel');
    }

    /**
     * Get personalized join link for the authenticated user
     * Returns moderator_link for teachers, attendee_link for students
     */
    public function getJoinLink(string $id)
    {
        return $this->executeService(function () use ($id) {
            $session = $this->service->getSessionById($id);
            $group = Group::findOrFail($session->group_id);
            $user = auth()->user();

            // Check if session is cancelled
            if ($session->is_cancelled) {
                return $this->errorResponse('This session has been cancelled', 422);
            }

            // Check if session has meeting links
            if (empty($session->moderator_link) && empty($session->attendee_link)) {
                return $this->errorResponse('No meeting link available for this session', 404);
            }

            // Check if user is a teacher in this group
            $isTeacher = $group->groupTeachers()->where('teacher_id', $user->id)->exists();

            // Check if user is a student in this group
            $isStudent = $group->groupStudents()
                ->where('student_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$isTeacher && !$isStudent) {
                return $this->errorResponse('You are not enrolled in this group', 403);
            }

            // Return appropriate link based on role
            if ($isTeacher) {
                return $this->okResponse([
                    'role' => 'moderator',
                    'join_url' => $session->moderator_link,
                    'meeting_id' => $session->meeting_id,
                    'meeting_password' => $session->meeting_password,
                    'meeting_provider' => $session->meeting_provider,
                ], "Moderator link retrieved successfully");
            }

            return $this->okResponse([
                'role' => 'attendee',
                'join_url' => $session->attendee_link,
                'meeting_id' => $session->meeting_id,
                'meeting_password' => $session->meeting_password,
                'meeting_provider' => $session->meeting_provider,
            ], "Join link retrieved successfully");

        }, 'GroupSessionController@getJoinLink');
    }
}
