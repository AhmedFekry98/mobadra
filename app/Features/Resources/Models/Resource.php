<?php

namespace App\Features\Resources\Models;

use App\Features\Grades\Models\Grade;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resource extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'type',
        'grade_id',
        'uploaded_by',
        'is_downloadable',
        'is_active',
        'download_count',
        'view_count',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('resource_file')
            ->singleFile()
            ->useDisk('media');

        $this->addMediaCollection('resource_thumbnail')
            ->singleFile()
            ->useDisk('media');
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
