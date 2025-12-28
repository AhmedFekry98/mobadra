<?php

namespace App\Features\Chat\Controllers;

use App\Features\AuthManagement\Transformers\ProfileResource;
use App\Features\Groups\Models\GroupStudent;
use App\Features\Groups\Models\GroupTeacher;
use App\Features\SystemManagements\Models\User;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChatParticipantController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get chat participants based on current user's role
     * For students: returns all teachers in their groups
     * For teachers: returns all students in their groups
     */
    public function index(Request $request)
    {
        return $this->executeService(function () use ($request) {
            $user = auth()->user();
            $userRole = $user->role?->name;

            if ($userRole === 'student') {
                // Get student's group IDs
                $groupIds = GroupStudent::where('student_id', $user->id)
                    ->where('status', 'active')
                    ->pluck('group_id');

                if ($groupIds->isEmpty()) {
                    return $this->okResponse([], "No groups found");
                }

                // Get all teachers in those groups
                $participantIds = GroupTeacher::whereIn('group_id', $groupIds)
                    ->pluck('teacher_id')
                    ->unique();

            } elseif ($userRole === 'teacher') {
                // Get teacher's group IDs
                $groupIds = GroupTeacher::where('teacher_id', $user->id)
                    ->pluck('group_id');

                if ($groupIds->isEmpty()) {
                    return $this->okResponse([], "No groups found");
                }

                // Get all students in those groups
                $participantIds = GroupStudent::whereIn('group_id', $groupIds)
                    ->where('status', 'active')
                    ->pluck('student_id')
                    ->unique();

            } else {
                return $this->okResponse([], "No participants available for your role");
            }

            $participants = User::whereIn('id', $participantIds)
                ->with('role')
                ->get();

            return $this->okResponse(
                ProfileResource::collection($participants),
                "Chat participants retrieved successfully"
            );
        }, 'ChatParticipantController@index');
    }
}
