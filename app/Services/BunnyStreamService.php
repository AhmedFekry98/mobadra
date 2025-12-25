<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BunnyStreamService
{
    protected ?string $libraryId;
    protected ?string $apiKey;
    protected ?string $cdnHostname;
    protected ?string $tokenKey;
    protected int $tokenExpiry;

    public function __construct()
    {
        $this->libraryId = config('services.bunny.stream_library_id');
        $this->apiKey = config('services.bunny.stream_api_key');
        $this->cdnHostname = config('services.bunny.stream_cdn_hostname');
        $this->tokenKey = config('services.bunny.token_authentication_key');
        $this->tokenExpiry = (int) config('services.bunny.token_expiry_seconds', 86400);
    }

    public function isConfigured(): bool
    {
        return !empty($this->libraryId)
            && !empty($this->cdnHostname)
            && !empty($this->tokenKey);
    }

    /**
     * Generate a signed URL for video playback with caching
     * Cache key includes video ID - same URL for all students (reduces API calls)
     */
    public function getSignedVideoUrl(string $videoId): string
    {
        $cacheKey = "bunny_video_url_{$videoId}";

        // Cache for 23 hours (1 hour before expiry for safety margin)
        $cacheDuration = $this->tokenExpiry - 3600;

        return Cache::remember($cacheKey, $cacheDuration, function () use ($videoId) {
            return $this->generateSignedUrl($videoId, 'playlist.m3u8');
        });
    }

    /**
     * Generate signed URL for video thumbnail with caching
     */
    public function getSignedThumbnailUrl(string $videoId): string
    {
        $cacheKey = "bunny_thumb_url_{$videoId}";
        $cacheDuration = $this->tokenExpiry - 3600;

        return Cache::remember($cacheKey, $cacheDuration, function () use ($videoId) {
            return $this->generateSignedUrl($videoId, 'thumbnail.jpg');
        });
    }

    /**
     * Generate signed URL (internal method)
     * CDN Token Auth: Base64(SHA256_RAW(security_key + url_path + expiration))
     */
    protected function generateSignedUrl(string $videoId, string $file): string
    {
        $expires = time() + $this->tokenExpiry;
        $path = "/{$videoId}/{$file}";

        // Bunny CDN Token Authentication
        // Format: Base64Encode(SHA256_RAW(token_security_key + signed_url + expiration))
        $hashableBase = $this->tokenKey . $path . $expires;

        // SHA256 raw binary output, then base64 encode
        $token = hash('sha256', $hashableBase, true);
        $token = base64_encode($token);

        // Replace characters for URL safety: + -> -, / -> _, remove =
        $token = strtr($token, '+/', '-_');
        $token = rtrim($token, '=');

        return "https://{$this->cdnHostname}{$path}?token={$token}&expires={$expires}";
    }

    /**
     * Get video details from Bunny Stream API
     */
    public function getVideoDetails(string $videoId): ?array
    {
        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
        ])->get("https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$videoId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Create a new video in Bunny Stream (for upload)
     */
    public function createVideo(string $title): ?array
    {
        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("https://video.bunnycdn.com/library/{$this->libraryId}/videos", [
            'title' => $title,
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Get direct upload URL for a video
     */
    public function getUploadUrl(string $videoId): string
    {
        return "https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$videoId}";
    }

    /**
     * Delete a video from Bunny Stream
     */
    public function deleteVideo(string $videoId): bool
    {
        $response = Http::withHeaders([
            'AccessKey' => $this->apiKey,
        ])->delete("https://video.bunnycdn.com/library/{$this->libraryId}/videos/{$videoId}");

        return $response->successful();
    }

    /**
     * Generate signed embed URL for iframe
     * Embed Token: SHA256_HEX(token_security_key + video_id + expiration)
     */
    public function getSignedEmbedUrl(string $videoId, array $options = []): string
    {
        $cacheKey = "bunny_embed_url_{$videoId}";
        $cacheDuration = $this->tokenExpiry - 3600;

        return Cache::remember($cacheKey, $cacheDuration, function () use ($videoId, $options) {
            $expires = time() + $this->tokenExpiry;

            // Embed view token: SHA256_HEX(token_security_key + video_id + expiration)
            $hashableBase = $this->tokenKey . $videoId . $expires;
            $token = hash('sha256', $hashableBase);

            // Build query parameters
            $params = array_merge([
                'token' => $token,
                'expires' => $expires,
                'autoplay' => $options['autoplay'] ?? 'true',
                'loop' => $options['loop'] ?? 'false',
                'muted' => $options['muted'] ?? 'false',
                'preload' => $options['preload'] ?? 'true',
                'responsive' => $options['responsive'] ?? 'true',
            ], $options);

            $queryString = http_build_query($params);

            return "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}?{$queryString}";
        });
    }

    /**
     * Get embed iframe HTML with responsive wrapper
     */
    public function getEmbedHtml(string $videoId, array $options = []): string
    {
        $embedUrl = $this->getSignedEmbedUrl($videoId, $options);

        return '<div style="position:relative;padding-top:56.25%;">' .
            '<iframe src="' . $embedUrl . '" ' .
            'loading="lazy" ' .
            'style="border:0;position:absolute;top:0;height:100%;width:100%;" ' .
            'allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" ' .
            'allowfullscreen="true"></iframe>' .
            '</div>';
    }
}
