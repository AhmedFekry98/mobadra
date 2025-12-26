<?php

namespace App\Features\Groups\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'zoom_registrant_id',
        'zoom_participant_id',
        'join_url',
        'first_join_time',
        'last_leave_time',
        'total_duration',
        'status',
        'join_leave_logs',
    ];

    protected $casts = [
        'first_join_time' => 'datetime',
        'last_leave_time' => 'datetime',
        'total_duration' => 'integer',
        'join_leave_logs' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(GroupSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تسجيل دخول الطالب للـ Meeting
     */
    public function recordJoin(): void
    {
        $logs = $this->join_leave_logs ?? [];
        $logs[] = [
            'action' => 'join',
            'time' => now()->toISOString(),
        ];

        $updateData = [
            'status' => 'joined',
            'join_leave_logs' => $logs,
        ];

        if (!$this->first_join_time) {
            $updateData['first_join_time'] = now();
        }

        $this->update($updateData);
    }

    /**
     * تسجيل خروج الطالب من الـ Meeting
     */
    public function recordLeave(): void
    {
        $logs = $this->join_leave_logs ?? [];
        $logs[] = [
            'action' => 'leave',
            'time' => now()->toISOString(),
        ];

        // حساب مدة الحضور
        $duration = $this->calculateDuration();

        $this->update([
            'status' => 'left',
            'last_leave_time' => now(),
            'total_duration' => $duration,
            'join_leave_logs' => $logs,
        ]);
    }

    /**
     * حساب إجمالي مدة الحضور بالدقائق
     */
    public function calculateDuration(): int
    {
        $logs = $this->join_leave_logs ?? [];
        $totalSeconds = 0;
        $lastJoinTime = null;

        foreach ($logs as $log) {
            if ($log['action'] === 'join') {
                $lastJoinTime = \Carbon\Carbon::parse($log['time']);
            } elseif ($log['action'] === 'leave' && $lastJoinTime) {
                $leaveTime = \Carbon\Carbon::parse($log['time']);
                $totalSeconds += $leaveTime->diffInSeconds($lastJoinTime);
                $lastJoinTime = null;
            }
        }

        // لو لسه في الـ meeting
        if ($lastJoinTime) {
            $totalSeconds += now()->diffInSeconds($lastJoinTime);
        }

        return (int) round($totalSeconds / 60);
    }

    /**
     * تحديث الحضور في جدول attendances
     */
    public function syncToAttendance(): void
    {
        $session = $this->session;

        // تحديد حالة الحضور بناءً على مدة الحضور
        $sessionDuration = $session->start_time && $session->end_time
            ? \Carbon\Carbon::parse($session->start_time)->diffInMinutes(\Carbon\Carbon::parse($session->end_time))
            : 60;

        $attendancePercentage = $sessionDuration > 0
            ? ($this->total_duration / $sessionDuration) * 100
            : 0;

        // تحديد الحالة
        $status = 'absent';
        if ($attendancePercentage >= 75) {
            $status = 'present';
        } elseif ($attendancePercentage >= 25) {
            $status = 'late';
        }

        // تحديث أو إنشاء سجل الحضور
        Attendance::updateOrCreate(
            [
                'session_id' => $this->session_id,
                'student_id' => $this->user_id,
            ],
            [
                'group_id' => $session->group_id,
                'status' => $status,
                'attended_at' => $this->first_join_time,
                'notes' => "Zoom attendance: {$this->total_duration} minutes ({$attendancePercentage}%)",
            ]
        );
    }
}
