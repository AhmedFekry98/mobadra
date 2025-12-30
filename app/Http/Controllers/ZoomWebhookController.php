<?php

namespace App\Http\Controllers;

use App\Features\Groups\Models\Attendance;
use App\Features\Groups\Models\GroupSession;
use App\Features\SystemManagements\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Handle URL validation (Zoom sends this when setting up webhook)
        if (isset($payload['event']) && $payload['event'] === 'endpoint.url_validation') {
            return $this->handleUrlValidation($payload);
        }

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            Log::warning('Zoom Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $payload['event'] ?? null;
        $eventData = $payload['payload']['object'] ?? [];

        Log::info("Zoom Webhook received: {$event}", ['data' => $eventData]);

        switch ($event) {
            case 'meeting.participant_joined':
                $this->handleParticipantJoined($eventData);
                break;

            case 'meeting.participant_left':
                $this->handleParticipantLeft($eventData);
                break;

            case 'meeting.ended':
                $this->handleMeetingEnded($eventData);
                break;

            case 'recording.completed':
                $this->handleRecordingCompleted($eventData);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleUrlValidation(array $payload): \Illuminate\Http\JsonResponse
    {
        $plainToken = $payload['payload']['plainToken'] ?? '';
        $secretToken = config('zoom.webhook_secret_token');

        $hashForValidation = hash_hmac('sha256', $plainToken, $secretToken);

        return response()->json([
            'plainToken' => $plainToken,
            'encryptedToken' => $hashForValidation,
        ]);
    }

    protected function verifyWebhookSignature(Request $request): bool
    {
        $secretToken = config('zoom.webhook_secret_token');

        if (empty($secretToken)) {
            Log::warning('Zoom Webhook: Secret token not configured');
            return true; // Allow in development if not configured
        }

        $signature = $request->header('x-zm-signature');
        $timestamp = $request->header('x-zm-request-timestamp');

        if (!$signature || !$timestamp) {
            return false;
        }

        $message = "v0:{$timestamp}:{$request->getContent()}";
        $hashForVerify = 'v0=' . hash_hmac('sha256', $message, $secretToken);

        return hash_equals($hashForVerify, $signature);
    }

    protected function handleParticipantJoined(array $data): void
    {
        $meetingId = $data['id'] ?? null;
        $participant = $data['participant'] ?? [];

        $email = $participant['email'] ?? null;
        $userName = $participant['user_name'] ?? null;
        $joinTime = $participant['join_time'] ?? now();

        if (!$meetingId) {
            return;
        }

        // Find the session by meeting_id
        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if (!$session) {
            Log::warning("Zoom Webhook: Session not found for meeting {$meetingId}");
            return;
        }

        // Find student by email
        $student = null;
        if ($email) {
            $student = User::where('email', $email)->first();
        }

        if (!$student) {
            Log::info("Zoom Webhook: Unknown participant joined - {$userName} ({$email})");
            return;
        }

        // Check if attendance already exists
        $existingAttendance = Attendance::where('session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingAttendance) {
            // Update if was absent
            if ($existingAttendance->status === 'absent') {
                $existingAttendance->update([
                    'status' => 'present',
                    'attended_at' => $joinTime,
                    'notes' => 'Joined via Zoom',
                ]);
            }
        } else {
            // Create new attendance record
            Attendance::create([
                'group_id' => $session->group_id,
                'session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'present',
                'attended_at' => $joinTime,
                'notes' => 'Joined via Zoom',
            ]);
        }

        Log::info("Zoom Webhook: Attendance recorded for student {$student->id} in session {$session->id}");
    }

    protected function handleParticipantLeft(array $data): void
    {
        $meetingId = $data['id'] ?? null;
        $participant = $data['participant'] ?? [];

        $email = $participant['email'] ?? null;
        $leaveTime = $participant['leave_time'] ?? now();

        if (!$meetingId || !$email) {
            return;
        }

        $session = GroupSession::where('meeting_id', $meetingId)->first();
        $student = User::where('email', $email)->first();

        if ($session && $student) {
            $attendance = Attendance::where('session_id', $session->id)
                ->where('student_id', $student->id)
                ->first();

            if ($attendance) {
                $attendance->update([
                    'notes' => ($attendance->notes ?? '') . " | Left at: {$leaveTime}",
                ]);
            }
        }
    }

    protected function handleMeetingEnded(array $data): void
    {
        $meetingId = $data['id'] ?? null;

        if (!$meetingId) {
            return;
        }

        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if ($session) {
            Log::info("Zoom Webhook: Meeting ended for session {$session->id}");
        }
    }

    protected function handleRecordingCompleted(array $data): void
    {
        $meetingId = $data['id'] ?? null;
        $recordingFiles = $data['recording_files'] ?? [];

        if (!$meetingId) {
            return;
        }

        $session = GroupSession::where('meeting_id', $meetingId)->first();

        if (!$session) {
            return;
        }

        // Find MP4 recording
        $mp4Recording = collect($recordingFiles)->firstWhere('file_type', 'MP4');

        if ($mp4Recording) {
            $session->update([
                'recording_url' => $mp4Recording['play_url'] ?? null,
                'recording_download_url' => $mp4Recording['download_url'] ?? null,
                'recording_password' => $data['password'] ?? null,
            ]);

            Log::info("Zoom Webhook: Recording saved for session {$session->id}");
        }
    }
}
