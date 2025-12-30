<?php

namespace App\Features\Courses\Models;

use App\Services\BunnyStreamService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_url', // This stores the Bunny video ID
        'video_provider',
        'duration',
        'thumbnail_url',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    protected $appends = ['signed_url', 'signed_thumbnail_url', 'embed_url', 'embed_html'];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }

    public function quiz()
    {
        return $this->hasOne(VideoQuiz::class);
    }

    public function hasQuiz(): bool
    {
        return $this->quiz()->exists();
    }

    /**
     * Get signed URL for video playback (protected)
     */
    public function getSignedUrlAttribute(): ?string
    {
        if ($this->video_provider !== 'bunny') {
            return $this->video_url;
        }

        $bunnyService = app(BunnyStreamService::class);

        if (!$bunnyService->isConfigured()) {
            return $this->video_url; // Return raw URL if Bunny not configured
        }

        return $bunnyService->getSignedVideoUrl($this->video_url);
    }

    /**
     * Get signed URL for thumbnail (protected)
     */
    public function getSignedThumbnailUrlAttribute(): ?string
    {
        if ($this->video_provider !== 'bunny' || !$this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        $bunnyService = app(BunnyStreamService::class);

        if (!$bunnyService->isConfigured()) {
            return $this->thumbnail_url; // Return raw URL if Bunny not configured
        }

        return $bunnyService->getSignedThumbnailUrl($this->video_url);
    }

    /**
     * Get signed embed URL for iframe
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->video_provider !== 'bunny') {
            return null;
        }

        $bunnyService = app(BunnyStreamService::class);

        if (!$bunnyService->isConfigured()) {
            return null;
        }

        return $bunnyService->getSignedEmbedUrl($this->video_url);
    }

    /**
     * Get embed HTML with responsive wrapper
     */
    public function getEmbedHtmlAttribute(): ?string
    {
        if ($this->video_provider !== 'bunny') {
            return null;
        }

        $bunnyService = app(BunnyStreamService::class);

        if (!$bunnyService->isConfigured()) {
            return null;
        }

        return $bunnyService->getEmbedHtml($this->video_url);
    }
}
