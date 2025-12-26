<?php

namespace App\Features\Groups\Models;

use App\Features\Courses\Models\LessonContent;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BunnyWatchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_content_id',
        'group_id',
        'video_id',
        'video_library_id',
        'watch_time',
        'video_duration',
        'percentage_watched',
        'country_code',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'session_id',
        'raw_data',
        'watched_at',
    ];

    protected $casts = [
        'watch_time' => 'integer',
        'video_duration' => 'integer',
        'percentage_watched' => 'integer',
        'raw_data' => 'array',
        'watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lessonContent()
    {
        return $this->belongsTo(LessonContent::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
