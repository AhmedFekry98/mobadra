<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Models\GroupStudent;
use App\Features\Groups\Models\SessionParticipant;
use Illuminate\Database\Eloquent\Collection;

class SessionParticipantService
{
    /**
     * تسجيل جميع طلاب المجموعة في الحصة
     */
    public function registerGroupStudents(int $sessionId): int
    {
        $session = GroupSession::with('group.groupStudents')->findOrFail($sessionId);
        $students = $session->group->groupStudents->where('status', 'active');

        $count = 0;
        foreach ($students as $groupStudent) {
            SessionParticipant::firstOrCreate(
                [
                    'session_id' => $sessionId,
                    'user_id' => $groupStudent->student_id,
                ],
                [
                    'status' => 'registered',
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * تسجيل طالب في حصة مع بيانات Zoom
     */
    public function registerParticipant(int $sessionId, int $userId, array $zoomData = []): SessionParticipant
    {
        return SessionParticipant::updateOrCreate(
            [
                'session_id' => $sessionId,
                'user_id' => $userId,
            ],
            [
                'zoom_registrant_id' => $zoomData['registrant_id'] ?? null,
                'join_url' => $zoomData['join_url'] ?? null,
                'status' => 'registered',
            ]
        );
    }

    /**
     * تسجيل دخول طالب للـ Meeting (من Zoom Webhook)
     */
    public function recordJoin(int $sessionId, string $participantId, ?string $email = null): ?SessionParticipant
    {
        // البحث بـ participant_id أو registrant_id
        $participant = SessionParticipant::where('session_id', $sessionId)
            ->where(function ($query) use ($participantId) {
                $query->where('zoom_participant_id', $participantId)
                    ->orWhere('zoom_registrant_id', $participantId);
            })
            ->first();

        // لو مش موجود، نبحث بالإيميل
        if (!$participant && $email) {
            $participant = $this->findByEmail($sessionId, $email);
        }

        if ($participant) {
            $participant->update(['zoom_participant_id' => $participantId]);
            $participant->recordJoin();
        }

        return $participant;
    }

    /**
     * تسجيل خروج طالب من الـ Meeting (من Zoom Webhook)
     */
    public function recordLeave(int $sessionId, string $participantId): ?SessionParticipant
    {
        $participant = SessionParticipant::where('session_id', $sessionId)
            ->where(function ($query) use ($participantId) {
                $query->where('zoom_participant_id', $participantId)
                    ->orWhere('zoom_registrant_id', $participantId);
            })
            ->first();

        if ($participant) {
            $participant->recordLeave();
        }

        return $participant;
    }

    /**
     * البحث عن participant بالإيميل
     */
    protected function findByEmail(int $sessionId, string $email): ?SessionParticipant
    {
        return SessionParticipant::where('session_id', $sessionId)
            ->whereHas('user', function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->first();
    }

    /**
     * جلب جميع المشاركين في حصة
     */
    public function getSessionParticipants(int $sessionId): Collection
    {
        return SessionParticipant::where('session_id', $sessionId)
            ->with('user')
            ->get();
    }

    /**
     * مزامنة جميع المشاركين مع جدول الحضور
     */
    public function syncAllToAttendance(int $sessionId): void
    {
        $participants = SessionParticipant::where('session_id', $sessionId)->get();

        foreach ($participants as $participant) {
            $participant->syncToAttendance();
        }
    }

    /**
     * جلب رابط الانضمام للطالب
     */
    public function getJoinUrl(int $sessionId, int $userId): ?string
    {
        $participant = SessionParticipant::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        return $participant?->join_url;
    }

    /**
     * تحديث روابط الانضمام لجميع المشاركين (من Zoom API)
     */
    public function updateJoinUrls(int $sessionId, array $registrants): void
    {
        foreach ($registrants as $registrant) {
            SessionParticipant::where('session_id', $sessionId)
                ->where('zoom_registrant_id', $registrant['id'])
                ->update([
                    'join_url' => $registrant['join_url'] ?? null,
                ]);
        }
    }
}
