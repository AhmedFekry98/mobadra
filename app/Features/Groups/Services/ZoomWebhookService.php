<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Models\SessionParticipant;
use Illuminate\Support\Facades\Log;

class ZoomWebhookService
{
    public function __construct(
        protected SessionParticipantService $participantService
    ) {}

    /**
     * معالجة Zoom Webhook
     */
    public function processWebhook(array $payload): void
    {
        $event = $payload['event'] ?? null;
        $data = $payload['payload'] ?? [];

        Log::info('Zoom webhook received', ['event' => $event]);

        match ($event) {
            'meeting.participant_joined' => $this->handleParticipantJoined($data),
            'meeting.participant_left' => $this->handleParticipantLeft($data),
            'meeting.ended' => $this->handleMeetingEnded($data),
            default => Log::info('Unhandled Zoom event', ['event' => $event]),
        };
    }

    /**
     * معالجة دخول مشارك
     */
    protected function handleParticipantJoined(array $data): void
    {
        $meetingId = $data['object']['id'] ?? null;
        $participant = $data['object']['participant'] ?? [];

        if (!$meetingId) {
            Log::warning('Zoom webhook: No meeting ID');
            return;
        }

        // البحث عن الـ session بـ meeting_id
        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if (!$session) {
            Log::warning('Zoom webhook: Session not found', ['meeting_id' => $meetingId]);
            return;
        }

        $participantId = $participant['participant_uuid'] ?? $participant['id'] ?? null;
        $email = $participant['email'] ?? null;

        if ($participantId) {
            $this->participantService->recordJoin($session->id, $participantId, $email);
            Log::info('Zoom: Participant joined', [
                'session_id' => $session->id,
                'participant_id' => $participantId,
                'email' => $email,
            ]);
        }
    }

    /**
     * معالجة خروج مشارك
     */
    protected function handleParticipantLeft(array $data): void
    {
        $meetingId = $data['object']['id'] ?? null;
        $participant = $data['object']['participant'] ?? [];

        if (!$meetingId) {
            return;
        }

        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if (!$session) {
            return;
        }

        $participantId = $participant['participant_uuid'] ?? $participant['id'] ?? null;

        if ($participantId) {
            $this->participantService->recordLeave($session->id, $participantId);
            Log::info('Zoom: Participant left', [
                'session_id' => $session->id,
                'participant_id' => $participantId,
            ]);
        }
    }

    /**
     * معالجة انتهاء الـ Meeting
     */
    protected function handleMeetingEnded(array $data): void
    {
        $meetingId = $data['object']['id'] ?? null;

        if (!$meetingId) {
            return;
        }

        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if (!$session) {
            return;
        }

        // مزامنة جميع المشاركين مع جدول الحضور
        $this->participantService->syncAllToAttendance($session->id);

        Log::info('Zoom: Meeting ended, attendance synced', ['session_id' => $session->id]);
    }
}
