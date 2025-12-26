<?php

namespace App\Features\Groups\Services;

use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\VideoContent;
use App\Features\Groups\Models\BunnyWatchLog;
use App\Features\Groups\Models\ContentProgress;
use App\Features\Groups\Models\GroupStudent;
use Illuminate\Support\Facades\Log;

class BunnyWebhookService
{
    public function __construct(
        protected ContentProgressService $progressService
    ) {}

    /**
     * Process Bunny Stream webhook data
     * Bunny يرسل بيانات المشاهدة عبر Webhook
     */
    public function processWebhook(array $data): void
    {
        try {
            // Extract data from Bunny webhook
            $videoId = $data['VideoGuid'] ?? $data['video_id'] ?? null;
            $libraryId = $data['VideoLibraryId'] ?? $data['library_id'] ?? null;
            $watchTime = $data['WatchTime'] ?? $data['watch_time'] ?? 0;
            $videoDuration = $data['VideoDuration'] ?? $data['duration'] ?? 0;
            $percentageWatched = $data['PercentageWatched'] ?? $data['percentage'] ?? 0;

            if (!$videoId) {
                Log::warning('Bunny webhook: No video ID provided', $data);
                return;
            }

            // Find the lesson content by video_url containing the video ID
            $lessonContent = $this->findLessonContentByVideoId($videoId);

            if (!$lessonContent) {
                Log::warning('Bunny webhook: Lesson content not found for video', ['video_id' => $videoId]);
                // Still log the watch data
                $this->createWatchLog(null, null, null, $videoId, $libraryId, $data);
                return;
            }

            // Try to identify the user (from custom data or session)
            $userId = $data['UserId'] ?? $data['user_id'] ?? null;
            $groupId = $data['GroupId'] ?? $data['group_id'] ?? null;

            // Create watch log
            $this->createWatchLog($userId, $lessonContent->id, $groupId, $videoId, $libraryId, $data);

            // Update content progress if user is identified
            if ($userId) {
                $this->updateContentProgress($userId, $lessonContent->id, $groupId, $percentageWatched, $watchTime);
            }

        } catch (\Exception $e) {
            Log::error('Bunny webhook processing error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
        }
    }

    /**
     * Find lesson content by Bunny video ID
     */
    protected function findLessonContentByVideoId(string $videoId): ?LessonContent
    {
        // البحث في video_contents عن الفيديو
        $videoContent = VideoContent::where('video_url', 'like', "%{$videoId}%")->first();

        if ($videoContent) {
            return LessonContent::where('contentable_type', VideoContent::class)
                ->where('contentable_id', $videoContent->id)
                ->first();
        }

        return null;
    }

    /**
     * Create watch log entry
     */
    protected function createWatchLog(
        ?int $userId,
        ?int $lessonContentId,
        ?int $groupId,
        string $videoId,
        ?string $libraryId,
        array $data
    ): BunnyWatchLog {
        return BunnyWatchLog::create([
            'user_id' => $userId,
            'lesson_content_id' => $lessonContentId,
            'group_id' => $groupId,
            'video_id' => $videoId,
            'video_library_id' => $libraryId,
            'watch_time' => $data['WatchTime'] ?? $data['watch_time'] ?? 0,
            'video_duration' => $data['VideoDuration'] ?? $data['duration'] ?? null,
            'percentage_watched' => $data['PercentageWatched'] ?? $data['percentage'] ?? 0,
            'country_code' => $data['CountryCode'] ?? $data['country'] ?? null,
            'device_type' => $data['DeviceType'] ?? $data['device'] ?? null,
            'browser' => $data['Browser'] ?? $data['browser'] ?? null,
            'os' => $data['OperatingSystem'] ?? $data['os'] ?? null,
            'ip_address' => $data['IpAddress'] ?? $data['ip'] ?? null,
            'session_id' => $data['SessionId'] ?? $data['session_id'] ?? null,
            'raw_data' => $data,
            'watched_at' => now(),
        ]);
    }

    /**
     * Update content progress based on watch data
     */
    protected function updateContentProgress(
        int $userId,
        int $lessonContentId,
        ?int $groupId,
        int $percentage,
        int $watchTime
    ): void {
        $this->progressService->updateProgress($userId, $lessonContentId, [
            'progress_percentage' => $percentage,
            'last_position' => 0, // Bunny doesn't provide exact position
            'watch_time' => $watchTime,
        ], $groupId);
    }

    /**
     * Process video progress from frontend (alternative to webhook)
     * يستخدم عندما الـ Frontend يرسل progress مباشرة
     */
    public function processVideoProgress(int $userId, array $data): ContentProgress
    {
        $lessonContentId = $data['lesson_content_id'];
        $groupId = $data['group_id'] ?? null;

        // Create watch log
        if (isset($data['video_id'])) {
            $this->createWatchLog(
                $userId,
                $lessonContentId,
                $groupId,
                $data['video_id'],
                $data['library_id'] ?? null,
                $data
            );
        }

        // Update progress
        return $this->progressService->updateProgress($userId, $lessonContentId, [
            'progress_percentage' => $data['progress_percentage'] ?? 0,
            'last_position' => $data['last_position'] ?? 0,
            'watch_time' => $data['watch_time'] ?? 0,
        ], $groupId);
    }
}
