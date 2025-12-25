<?php

namespace App\Features\Groups\Models;

use App\Features\Courses\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'session_date',
        'start_time',
        'end_time',
        'topic',
        'lesson_id',
        'session_type',
        'session_number',
        'is_cancelled',
        'cancellation_reason',
        'meeting_provider',
        'meeting_id',
        'meeting_password',
        'moderator_link',
        'attendee_link',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_cancelled' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    public function presentStudents()
    {
        return $this->attendances()->where('status', 'present');
    }

    public function absentStudents()
    {
        return $this->attendances()->where('status', 'absent');
    }
}
