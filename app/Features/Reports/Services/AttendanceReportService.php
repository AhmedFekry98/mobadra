<?php

namespace App\Features\Reports\Services;

use App\Features\Groups\Models\Attendance;
use App\Features\Groups\Models\GroupSession;
use App\Features\SystemManagements\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceReportService
{
    public function getAttendanceReport(array $filters): array
    {
        $query = Attendance::query()
            ->with(['student', 'session.group', 'group']);

        // Filter by student
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        // Filter by session type (online/offline)
        if (!empty($filters['session_type'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('session_type', $filters['session_type']);
            });
        }

        // Date filters
        $this->applyDateFilters($query, $filters);

        $attendances = $query->orderBy('attended_at', 'desc')->get();

        return $this->formatAttendanceReport($attendances, $filters);
    }

    public function getStudentAttendanceReport(int $studentId, array $filters): array
    {
        $filters['student_id'] = $studentId;

        $query = Attendance::query()
            ->where('student_id', $studentId)
            ->with(['session.group', 'group']);

        // Filter by session type
        if (!empty($filters['session_type'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('session_type', $filters['session_type']);
            });
        }

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        $this->applyDateFilters($query, $filters);

        $attendances = $query->orderBy('attended_at', 'desc')->get();

        $student = User::find($studentId);

        $totalSessions = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();

        return [
            'student' => [
                'id' => $student?->id,
                'name' => $student?->name,
                'email' => $student?->email,
            ],
            'summary' => [
                'total_sessions' => $totalSessions,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'excused' => $excusedCount,
                'attendance_rate' => $totalSessions > 0
                    ? round(($presentCount + $lateCount) / $totalSessions * 100, 2)
                    : 0,
            ],
            'by_session_type' => $this->groupBySessionType($attendances),
            'details' => $attendances->map(fn($a) => [
                'id' => $a->id,
                'date' => $a->attended_at?->toDateString(),
                'status' => $a->status,
                'session' => [
                    'id' => $a->session?->id,
                    'topic' => $a->session?->topic,
                    'type' => $a->session?->session_type,
                    'date' => $a->session?->session_date?->toDateString(),
                ],
                'group' => [
                    'id' => $a->group?->id,
                    'name' => $a->group?->name,
                ],
                'notes' => $a->notes,
            ])->values(),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    public function getAllStudentsAttendanceReport(array $filters): array
    {
        $query = Attendance::query()
            ->with(['student', 'session.group', 'group']);

        // Filter by session type
        if (!empty($filters['session_type'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('session_type', $filters['session_type']);
            });
        }

        // Filter by group
        if (!empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        $this->applyDateFilters($query, $filters);

        $attendances = $query->get();

        // Group by student
        $byStudent = $attendances->groupBy('student_id');

        $studentsReport = $byStudent->map(function ($studentAttendances, $studentId) {
            $student = $studentAttendances->first()->student;
            $total = $studentAttendances->count();
            $present = $studentAttendances->where('status', 'present')->count();
            $absent = $studentAttendances->where('status', 'absent')->count();
            $late = $studentAttendances->where('status', 'late')->count();

            return [
                'student_id' => $studentId,
                'student_name' => $student?->name,
                'total_sessions' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 2) : 0,
            ];
        })->sortByDesc('attendance_rate')->values();

        return [
            'total_students' => $studentsReport->count(),
            'overall_summary' => [
                'total_records' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'average_attendance_rate' => $studentsReport->avg('attendance_rate') ?? 0,
            ],
            'students' => $studentsReport,
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function applyDateFilters($query, array $filters): void
    {
        // This week
        if (!empty($filters['period']) && $filters['period'] === 'this_week') {
            $query->whereBetween('attended_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        }

        // This month
        if (!empty($filters['period']) && $filters['period'] === 'this_month') {
            $query->whereBetween('attended_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        // Custom date range
        if (!empty($filters['date_from'])) {
            $query->where('attended_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('attended_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
    }

    protected function formatAttendanceReport(Collection $attendances, array $filters): array
    {
        $totalSessions = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();

        return [
            'summary' => [
                'total_records' => $totalSessions,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'attendance_rate' => $totalSessions > 0
                    ? round(($presentCount + $lateCount) / $totalSessions * 100, 2)
                    : 0,
            ],
            'by_session_type' => $this->groupBySessionType($attendances),
            'filters' => $this->getAppliedFilters($filters),
        ];
    }

    protected function groupBySessionType(Collection $attendances): array
    {
        $byType = $attendances->groupBy(fn($a) => $a->session?->session_type ?? 'unknown');

        return $byType->map(function ($typeAttendances, $type) {
            $total = $typeAttendances->count();
            $present = $typeAttendances->where('status', 'present')->count();
            $late = $typeAttendances->where('status', 'late')->count();

            return [
                'type' => $type,
                'total' => $total,
                'present' => $present,
                'absent' => $typeAttendances->where('status', 'absent')->count(),
                'late' => $late,
                'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 2) : 0,
            ];
        })->values()->toArray();
    }

    protected function getAppliedFilters(array $filters): array
    {
        return array_filter([
            'student_id' => $filters['student_id'] ?? null,
            'group_id' => $filters['group_id'] ?? null,
            'session_type' => $filters['session_type'] ?? null,
            'period' => $filters['period'] ?? null,
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ]);
    }
}
