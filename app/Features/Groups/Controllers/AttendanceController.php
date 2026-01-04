<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Requests\AttendanceRequest;
use App\Features\Groups\Requests\BulkAttendanceRequest;
use App\Features\Groups\Services\AttendanceService;
use App\Features\Groups\Services\GroupSessionService;
use App\Features\Groups\Transformers\AttendanceResource;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;

class AttendanceController extends Controller
{
    use ApiResponses, HandleServiceExceptions, AuthorizesRequests;

    public function __construct(
        protected AttendanceService $service,
        protected GroupSessionService $sessionService
    ) {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return $this->executeService(function () {
            $user = auth()->user();
            $attendances = $this->service->getAllAttendance(
                user: $user,
                paginate: request()->has('page')
            );

            return $this->okResponse(
                request()->has('page')
                    ? AttendanceResource::collection($attendances)
                    : AttendanceResource::collection($attendances),
                "Attendance retrieved successfully"
            );
        }, 'AttendanceController@index');
    }

    public function getBySession(string $sessionId)
    {
        return $this->executeService(function () use ($sessionId) {
            $user = auth()->user();
            $attendances = $this->service->getAttendanceBySession($sessionId, $user);

            return $this->okResponse(
                AttendanceResource::collection($attendances),
                "Attendance retrieved successfully"
            );
        }, 'AttendanceController@getBySession');
    }

    public function recordAttendance(AttendanceRequest $request, string $sessionId)
    {
        return $this->executeService(function () use ($request, $sessionId) {
            $data = $request->validated();
            $attendance = $this->service->recordAttendance(
                $sessionId,
                $data['student_id'],
                $data['status'],
                $data['notes'] ?? null,
                auth()->user()->id
            );

            return $this->okResponse(
                AttendanceResource::make($attendance->load('student')),
                "Attendance recorded successfully"
            );
        }, 'AttendanceController@recordAttendance');
    }

    public function bulkRecordAttendance(BulkAttendanceRequest $request, string $sessionId)
    {
        return $this->executeService(function () use ($request, $sessionId) {
            $attendances = $this->service->bulkRecordAttendance(
                $sessionId,
                $request->validated()['attendances'],
                auth()->user()->id
            );

            return $this->okResponse(
                AttendanceResource::collection($attendances),
                "Attendance recorded successfully"
            );
        }, 'AttendanceController@bulkRecordAttendance');
    }

    public function initializeSession(string $sessionId)
    {
        return $this->executeService(function () use ($sessionId) {
            $attendances = $this->service->initializeSessionAttendance($sessionId);

            return $this->okResponse(
                AttendanceResource::collection($attendances),
                "Session attendance initialized successfully"
            );
        }, 'AttendanceController@initializeSession');
    }

    public function update(AttendanceRequest $request, string $id)
    {
        return $this->executeService(function () use ($request, $id) {
            $attendance = $this->service->updateAttendance($id, $request->validated());

            return $this->okResponse(
                AttendanceResource::make($attendance->load('student')),
                "Attendance updated successfully"
            );
        }, 'AttendanceController@update');
    }

    public function getSessionStats(string $sessionId)
    {
        return $this->executeService(function () use ($sessionId) {
            $stats = $this->service->getSessionAttendanceStats($sessionId);

            return $this->okResponse(
                $stats,
                "Session attendance stats retrieved successfully"
            );
        }, 'AttendanceController@getSessionStats');
    }

    public function getGroupReport(string $groupId)
    {
        return $this->executeService(function () use ($groupId) {
            $report = $this->service->getGroupAttendanceReport($groupId);

            return $this->okResponse(
                $report,
                "Group attendance report retrieved successfully"
            );
        }, 'AttendanceController@getGroupReport');
    }
}
