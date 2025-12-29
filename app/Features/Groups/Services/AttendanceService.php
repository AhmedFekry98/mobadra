<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\Attendance;
use App\Features\Groups\Repositories\AttendanceRepository;
use App\Features\Groups\Repositories\GroupStudentRepository;
use App\Features\Groups\Repositories\GroupSessionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function __construct(
        protected AttendanceRepository $repository,
        protected GroupStudentRepository $groupStudentRepository,
        protected GroupSessionRepository $sessionRepository
    ) {}

    public function getAttendanceBySession(string $sessionId): Collection
    {
        return $this->repository->getBySessionId($sessionId);
    }

    public function getAttendanceByGroup(string $groupId): Collection
    {
        return $this->repository->getByGroupId($groupId);
    }

    public function getAttendanceByStudent(string $studentId): Collection
    {
        return $this->repository->getByStudentId($studentId);
    }

    public function recordAttendance(string $sessionId, string $studentId, string $status, ?string $notes = null, ?string $recordedBy = null): Attendance
    {
        $session = $this->sessionRepository->findOrFail($sessionId);

        return $this->repository->updateOrCreate(
            [
                'session_id' => $sessionId,
                'student_id' => $studentId,
            ],
            [
                'group_id' => $session->group_id,
                'status' => $status,
                'attended_at' => $status === 'present' || $status === 'late' ? now() : null,
                'notes' => $notes,
                'recorded_by' => $recordedBy,
            ]
        );
    }

    public function bulkRecordAttendance(string $sessionId, array $attendanceData, ?string $recordedBy = null): Collection
    {
        return DB::transaction(function () use ($sessionId, $attendanceData, $recordedBy) {
            $session = $this->sessionRepository->findOrFail($sessionId);
            $results = [];

            foreach ($attendanceData as $record) {
                $results[] = $this->repository->updateOrCreate(
                    [
                        'session_id' => $sessionId,
                        'student_id' => $record['student_id'],
                    ],
                    [
                        'group_id' => $session->group_id,
                        'status' => $record['status'],
                        'attended_at' => in_array($record['status'], ['present', 'late']) ? now() : null,
                        'notes' => $record['notes'] ?? null,
                        'recorded_by' => $recordedBy,
                    ]
                );
            }

            $collection = new Collection($results);
            $collection->load('student');
            return $collection;
        });
    }

    public function initializeSessionAttendance(string $sessionId): Collection
    {
        return DB::transaction(function () use ($sessionId) {
            $session = $this->sessionRepository->findOrFail($sessionId);
            $students = $this->groupStudentRepository->getActiveByGroupId($session->group_id);
            $results = [];

            foreach ($students as $groupStudent) {
                $results[] = $this->repository->updateOrCreate(
                    [
                        'session_id' => $sessionId,
                        'student_id' => $groupStudent->student_id,
                    ],
                    [
                        'group_id' => $session->group_id,
                        'status' => 'absent',
                    ]
                );
            }

            $collection = new Collection($results);
            $collection->load('student');
            return $collection;
        });
    }

    public function updateAttendance(string $id, array $data): Attendance
    {
        return $this->repository->update($id, $data);
    }

    public function getStudentAttendanceRate(string $groupId, string $studentId): array
    {
        return $this->repository->getStudentAttendanceRate($groupId, $studentId);
    }

    public function getSessionAttendanceStats(string $sessionId): array
    {
        return $this->repository->getSessionAttendanceStats($sessionId);
    }

    public function getGroupAttendanceReport(string $groupId): array
    {
        $students = $this->groupStudentRepository->getActiveByGroupId($groupId);
        $report = [];

        foreach ($students as $groupStudent) {
            $report[] = [
                'student' => $groupStudent->student,
                'attendance' => $this->repository->getStudentAttendanceRate($groupId, $groupStudent->student_id),
            ];
        }

        return $report;
    }
}
