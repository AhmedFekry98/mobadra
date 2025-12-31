<?php

namespace App\Features\Groups\Models;

use App\Features\Courses\Models\Course;
use App\Features\Grades\Models\Grade;
use App\Features\SystemManagements\Models\Governorate;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'grade_id',
        'governorate_id',
        'name',
        'max_capacity',
        'days',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'location_type',
        'location_map_url',
        'is_active',
    ];

    protected $casts = [
        'days' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_students', 'group_id', 'student_id')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps();
    }

    public function groupStudents()
    {
        return $this->hasMany(GroupStudent::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'group_teachers', 'group_id', 'teacher_id')
            ->withPivot(['assigned_at', 'is_primary'])
            ->withTimestamps();
    }

    public function groupTeachers()
    {
        return $this->hasMany(GroupTeacher::class);
    }

    public function sessions()
    {
        return $this->hasMany(GroupSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function primaryTeacher()
    {
        return $this->teachers()->wherePivot('is_primary', true)->first();
    }

    public function activeStudentsCount(): int
    {
        return $this->groupStudents()->where('status', 'active')->count();
    }

    public function hasCapacity(): bool
    {
        return $this->activeStudentsCount() < $this->max_capacity;
    }

    public function availableSlots(): int
    {
        return $this->max_capacity - $this->activeStudentsCount();
    }
}
