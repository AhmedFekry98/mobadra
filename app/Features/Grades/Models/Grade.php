<?php

namespace App\Features\Grades\Models;

use App\Features\Courses\Models\Course;
use App\Features\Groups\Models\Group;
use App\Features\SystemManagements\Models\UserInformation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'min_age',
        'max_age',
        'order',
        'is_active',
    ];

    protected $casts = [
        'min_age' => 'integer',
        'max_age' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function userInformations()
    {
        return $this->hasMany(UserInformation::class);
    }

    public function getAgeRangeAttribute(): string
    {
        if ($this->min_age && $this->max_age) {
            return "{$this->min_age}-{$this->max_age}";
        }
        if ($this->min_age) {
            return "{$this->min_age}+";
        }
        if ($this->max_age) {
            return "Up to {$this->max_age}";
        }
        return 'N/A';
    }

    public function isAgeEligible(int $age): bool
    {
        if ($this->min_age && $age < $this->min_age) {
            return false;
        }
        if ($this->max_age && $age > $this->max_age) {
            return false;
        }
        return true;
    }
}
