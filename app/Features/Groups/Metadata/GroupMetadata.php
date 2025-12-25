<?php

namespace App\Features\Groups\Metadata;

use App\Features\Courses\Models\Course;

class GroupMetadata
{
    public static function get(): array
    {
        return [
            'courses' => Course::where('is_active', true)->get(['id', 'title']),
            'days' => [
                ['value' => 'sunday', 'label' => 'Sunday'],
                ['value' => 'monday', 'label' => 'Monday'],
                ['value' => 'tuesday', 'label' => 'Tuesday'],
                ['value' => 'wednesday', 'label' => 'Wednesday'],
                ['value' => 'thursday', 'label' => 'Thursday'],
                ['value' => 'friday', 'label' => 'Friday'],
                ['value' => 'saturday', 'label' => 'Saturday'],
            ],
            'student_statuses' => [
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'dropped', 'label' => 'Dropped'],
                ['value' => 'completed', 'label' => 'Completed'],
            ],
            'attendance_statuses' => [
                ['value' => 'present', 'label' => 'Present'],
                ['value' => 'absent', 'label' => 'Absent'],
                ['value' => 'late', 'label' => 'Late'],
                ['value' => 'excused', 'label' => 'Excused'],
            ],
            'location_types' => [
                ['value' => 'online', 'label' => 'Online'],
                ['value' => 'offline', 'label' => 'offline'],
            ],
            'meeting_providers' => [
                ['value' => 'zoom', 'label' => 'Zoom'],
                ['value' => 'google_meet', 'label' => 'Google Meet'],
                ['value' => 'teams', 'label' => 'Microsoft Teams'],
                ['value' => 'other', 'label' => 'Other'],
            ],
        ];
    }
}
