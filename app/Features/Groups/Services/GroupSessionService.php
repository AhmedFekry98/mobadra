<?php

namespace App\Features\Groups\Services;

use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Repositories\GroupSessionRepository;
use App\Services\ZoomService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GroupSessionService
{
    public function __construct(
        protected GroupSessionRepository $repository,
        protected ZoomService $zoomService
    ) {}

    public function getAllSessions(?bool $paginate = false, ?string $type = null): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($paginate, $type);
    }

    public function getSessionsByGroup(string $groupId, ?string $type = null): Collection
    {
        return $this->repository->getByGroupId($groupId, $type);
    }

    public function getSessionById(string $id): ?GroupSession
    {
        return $this->repository->findOrFail($id);
    }

    public function storeSession(array $data): GroupSession
    {
        $session = $this->repository->create($data);

        // Create Zoom meeting if meeting_provider is zoom
        if (($data['meeting_provider'] ?? null) === 'zoom') {
            $this->createZoomMeeting($session);
        }

        return $session->fresh();
    }

    public function createZoomMeeting(GroupSession $session): GroupSession
    {
        $startDateTime = Carbon::parse($session->session_date->format('Y-m-d') . ' ' . $session->start_time);

        $duration = 60; // default
        if ($session->start_time && $session->end_time) {
            $start = Carbon::parse($session->start_time);
            $end = Carbon::parse($session->end_time);
            $duration = $start->diffInMinutes($end);
        }

        $meeting = $this->zoomService->createMeeting([
            'topic' => $session->topic ?? "Session #{$session->session_number}",
            'start_time' => $startDateTime->toIso8601String(),
            'duration' => $duration,
        ]);

        $session->update([
            'meeting_id' => $meeting['meeting_id'],
            'meeting_password' => $meeting['password'],
            'moderator_link' => $meeting['start_url'],
            'attendee_link' => $meeting['join_url'],
        ]);

        return $session;
    }

    public function updateSession(string $id, array $data): GroupSession
    {
        return $this->repository->update($id, $data);
    }

    public function deleteSession(string $id): bool
    {
        $session = $this->repository->findOrFail($id);

        // Delete Zoom meeting if exists
        if ($session->meeting_provider === 'zoom' && $session->meeting_id) {
            try {
                $this->zoomService->deleteMeeting($session->meeting_id);
            } catch (\Exception $e) {
                // Log error but continue with deletion
                Log::warning("Failed to delete Zoom meeting: " . $e->getMessage());
            }
        }

        return $this->repository->delete($id);
    }

    public function cancelSession(string $id, ?string $reason = null): GroupSession
    {
        return $this->repository->update($id, [
            'is_cancelled' => true,
            'cancellation_reason' => $reason,
        ]);
    }

    public function getUpcomingSessions(string $groupId): Collection
    {
        return $this->repository->getUpcomingSessions($groupId);
    }

    public function getPastSessions(string $groupId): Collection
    {
        return $this->repository->getPastSessions($groupId);
    }

    public function getSessionRecordings(string $sessionId): array
    {
        $session = $this->repository->findOrFail($sessionId);

        if (!$session->meeting_id) {
            return [
                'has_recordings' => false,
                'message' => 'This session does not have a Zoom meeting.',
                'recordings' => [],
            ];
        }

        $recordings = $this->zoomService->getRecordings($session->meeting_id);

        // If recordings found and not already saved, save to session
        if ($recordings['has_recordings'] && !$session->recording_url) {
            $mainRecording = collect($recordings['recordings'])
                ->firstWhere('file_type', 'MP4');

            if ($mainRecording) {
                $session->update([
                    'recording_url' => $mainRecording['play_url'],
                    'recording_download_url' => $mainRecording['download_url'],
                    'recording_password' => $recordings['password'] ?? null,
                ]);
            }
        }

        return $recordings;
    }

    public function deleteSessionRecordings(string $sessionId): bool
    {
        $session = $this->repository->findOrFail($sessionId);

        if (!$session->meeting_id) {
            throw new \Exception('This session does not have a Zoom meeting.');
        }

        $this->zoomService->deleteRecordings($session->meeting_id);

        // Clear recording fields
        $session->update([
            'recording_url' => null,
            'recording_download_url' => null,
            'recording_password' => null,
        ]);

        return true;
    }
}
