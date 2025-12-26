<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ZoomService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $accountId;
    protected string $baseUrl;
    protected string $oauthUrl;

    public function __construct()
    {
        $this->clientId = config('zoom.client_id');
        $this->clientSecret = config('zoom.client_secret');
        $this->accountId = config('zoom.account_id');
        $this->baseUrl = config('zoom.base_url', 'https://api.zoom.us/v2');
        $this->oauthUrl = config('zoom.oauth_url', 'https://zoom.us/oauth/token');
    }

    protected function isConfigured(): bool
    {
        return !empty($this->clientId) && !empty($this->clientSecret) && !empty($this->accountId);
    }

    protected function getAccessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Zoom API is not configured. Please set ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, and ZOOM_ACCOUNT_ID in .env');
        }

        return Cache::remember('zoom_access_token', 3500, function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->oauthUrl, [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->failed()) {
                throw new \Exception('Failed to get Zoom access token: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    public function createMeeting(array $data): array
    {
        $token = $this->getAccessToken();

        $meetingData = [
            'topic' => $data['topic'] ?? 'Meeting',
            'type' => 2, // Scheduled meeting
            'start_time' => $data['start_time'], // ISO 8601 format
            'duration' => $data['duration'] ?? 60, // minutes
            'timezone' => $data['timezone'] ?? 'Africa/Cairo',
            'settings' => [
                'host_video' => $data['host_video'] ?? true,
                'participant_video' => $data['participant_video'] ?? true,
                'join_before_host' => $data['join_before_host'] ?? false,
                'mute_upon_entry' => $data['mute_upon_entry'] ?? true,
                'waiting_room' => $data['waiting_room'] ?? true,
                'auto_recording' => $data['auto_recording'] ?? 'cloud', // none, local, cloud
            ],
        ];

        if (!empty($data['password'])) {
            $meetingData['password'] = $data['password'];
        }

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/me/meetings", $meetingData);

        if ($response->failed()) {
            throw new \Exception('Failed to create Zoom meeting: ' . $response->body());
        }

        $meeting = $response->json();

        return [
            'meeting_id' => $meeting['id'],
            'join_url' => $meeting['join_url'],
            'start_url' => $meeting['start_url'],
            'password' => $meeting['password'] ?? null,
        ];
    }

    public function getMeeting(string $meetingId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/meetings/{$meetingId}");

        if ($response->failed()) {
            throw new \Exception('Failed to get Zoom meeting: ' . $response->body());
        }

        return $response->json();
    }

    public function updateMeeting(string $meetingId, array $data): bool
    {
        $token = $this->getAccessToken();

        $updateData = [];

        if (isset($data['topic'])) {
            $updateData['topic'] = $data['topic'];
        }

        if (isset($data['start_time'])) {
            $updateData['start_time'] = $data['start_time'];
        }

        if (isset($data['duration'])) {
            $updateData['duration'] = $data['duration'];
        }

        $response = Http::withToken($token)
            ->patch("{$this->baseUrl}/meetings/{$meetingId}", $updateData);

        if ($response->failed()) {
            throw new \Exception('Failed to update Zoom meeting: ' . $response->body());
        }

        return true;
    }

    public function deleteMeeting(string $meetingId): bool
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");

        if ($response->failed()) {
            throw new \Exception('Failed to delete Zoom meeting: ' . $response->body());
        }

        return true;
    }

    public function endMeeting(string $meetingId): bool
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->put("{$this->baseUrl}/meetings/{$meetingId}/status", [
                'action' => 'end',
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to end Zoom meeting: ' . $response->body());
        }

        return true;
    }

    public function getRecordings(string $meetingId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/meetings/{$meetingId}/recordings");

        if ($response->failed()) {
            // No recordings yet or meeting not ended
            if ($response->status() === 404) {
                return [
                    'has_recordings' => false,
                    'message' => 'No recordings available yet. Recordings are available after the meeting ends.',
                    'recordings' => [],
                ];
            }
            throw new \Exception('Failed to get Zoom recordings: ' . $response->body());
        }

        $data = $response->json();
        $recordings = [];

        foreach ($data['recording_files'] ?? [] as $file) {
            $recordings[] = [
                'id' => $file['id'],
                'type' => $file['recording_type'], // shared_screen_with_speaker_view, audio_only, etc.
                'file_type' => $file['file_type'], // MP4, M4A, etc.
                'file_size' => $file['file_size'],
                'play_url' => $file['play_url'] ?? null,
                'download_url' => $file['download_url'] ?? null,
                'status' => $file['status'], // completed, processing
                'recording_start' => $file['recording_start'],
                'recording_end' => $file['recording_end'],
            ];
        }

        return [
            'has_recordings' => count($recordings) > 0,
            'meeting_id' => $data['id'] ?? $meetingId,
            'topic' => $data['topic'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'duration' => $data['duration'] ?? null,
            'total_size' => $data['total_size'] ?? 0,
            'share_url' => $data['share_url'] ?? null,
            'password' => $data['password'] ?? null,
            'recordings' => $recordings,
        ];
    }

    public function deleteRecordings(string $meetingId): bool
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}/recordings");

        if ($response->failed()) {
            throw new \Exception('Failed to delete Zoom recordings: ' . $response->body());
        }

        return true;
    }
}
