<?php

namespace App\Features\Groups\Repositories;

use App\Features\Groups\Models\Attendance;
use App\Features\Groups\Queries\AttendanceRoleQuery;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceRepository
{
    public function query()
    {
        return Attendance::query();
    }

    public function getAll(User $user, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = AttendanceRoleQuery::resolve($user)
            ->with(['student', 'session', 'group']);

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function getBySessionId(string $sessionId, ?User $user = null): Collection
    {
        $query = $user
            ? AttendanceRoleQuery::resolve($user)->where('session_id', $sessionId)
            : Attendance::where('session_id', $sessionId);

        return $query->with('student')->get();
    }

    public function getByGroupId(string $groupId, ?User $user = null): Collection
    {
        $query = $user
            ? AttendanceRoleQuery::resolve($user)->where('group_id', $groupId)
            : Attendance::where('group_id', $groupId);

        return $query->with(['session', 'student'])->get();
    }

    public function getByStudentId(string $studentId): Collection
    {
        return Attendance::where('student_id', $studentId)
            ->with(['group', 'session'])
            ->get();
    }

    public function find(string $id): ?Attendance
    {
        return Attendance::find($id);
    }

    public function findOrFail(string $id): Attendance
    {
        return Attendance::findOrFail($id);
    }

    public function findBySessionAndStudent(string $sessionId, string $studentId): ?Attendance
    {
        return Attendance::where('session_id', $sessionId)
            ->where('student_id', $studentId)
            ->first();
    }

    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(string $id, array $data): Attendance
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function updateOrCreate(array $attributes, array $values): Attendance
    {
        return Attendance::updateOrCreate($attributes, $values);
    }

    public function delete(string $id): bool
    {
        return Attendance::destroy($id);
    }

    public function getStudentAttendanceRate(string $groupId, string $studentId): array
    {
        $total = Attendance::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->count();

        $present = Attendance::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->where('status', 'present')
            ->count();

        $absent = Attendance::where('group_id', $groupId)
            ->where('student_id', $studentId)
            ->where('status', 'absent')
            ->count();

        return [
            'total_sessions' => $total,
            'present' => $present,
            'absent' => $absent,
            'rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0,
        ];
    }

    public function getSessionAttendanceStats(string $sessionId): array
    {
        $total = Attendance::where('session_id', $sessionId)->count();
        $present = Attendance::where('session_id', $sessionId)->where('status', 'present')->count();
        $absent = Attendance::where('session_id', $sessionId)->where('status', 'absent')->count();
        $late = Attendance::where('session_id', $sessionId)->where('status', 'late')->count();
        $excused = Attendance::where('session_id', $sessionId)->where('status', 'excused')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
        ];
    }
}
