<?php

namespace Database\Seeders\Groups;

use App\Features\Courses\Models\Course;
use App\Features\Grades\Models\Grade;
use App\Features\Groups\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $introCourse = Course::where('slug', 'intro-to-programming')->first();
        $webCourse = Course::where('slug', 'web-dev-basics')->first();

        $grade1 = Grade::where('code', 'G1')->first();
        $grade2 = Grade::where('code', 'G2')->first();

        $groups = [
            // ========== ONLINE GROUPS - Grade 1 ==========
            // Schedule 1: Sunday/Tuesday 14:00-16:00 (2 groups same schedule)
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Online G1 - Sun/Tue 14:00 - A',
                'max_capacity' => 25,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => null,
                'location_type' => 'online',
                'location_map_url' => null,
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Online G1 - Sun/Tue 14:00 - B',
                'max_capacity' => 25,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => null,
                'location_type' => 'online',
                'location_map_url' => null,
                'is_active' => true,
            ],
            // Schedule 2: Friday/Saturday 10:00-12:00
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Online G1 - Fri/Sat 10:00 - A',
                'max_capacity' => 20,
                'days' => ['friday', 'saturday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => null,
                'location_type' => 'online',
                'location_map_url' => null,
                'is_active' => true,
            ],

            // ========== OFFLINE GROUPS - Grade 1 ==========
            // Location 1: Cairo Center - Sunday/Tuesday 14:00-16:00
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Offline G1 - Cairo - Sun/Tue 14:00 - A',
                'max_capacity' => 30,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Cairo Center',
                'location_type' => 'offline',
                'location_map_url' => 'https://maps.google.com/?q=cairo-center',
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Offline G1 - Cairo - Sun/Tue 14:00 - B',
                'max_capacity' => 30,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Cairo Center',
                'location_type' => 'offline',
                'location_map_url' => 'https://maps.google.com/?q=cairo-center',
                'is_active' => true,
            ],
            // Location 2: Giza Branch - Sunday/Tuesday 14:00-16:00
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Offline G1 - Giza - Sun/Tue 14:00 - A',
                'max_capacity' => 25,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Giza Branch',
                'location_type' => 'offline',
                'location_map_url' => 'https://maps.google.com/?q=giza-branch',
                'is_active' => true,
            ],
            // Location 1: Cairo Center - Friday/Saturday 10:00-12:00
            [
                'course_id' => $introCourse?->id,
                'grade_id' => $grade1?->id,
                'name' => 'Offline G1 - Cairo - Fri/Sat 10:00 - A',
                'max_capacity' => 30,
                'days' => ['friday', 'saturday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => 'Cairo Center',
                'location_type' => 'offline',
                'location_map_url' => 'https://maps.google.com/?q=cairo-center',
                'is_active' => true,
            ],

            // ========== ONLINE GROUPS - Grade 2 ==========
            [
                'course_id' => $webCourse?->id,
                'grade_id' => $grade2?->id,
                'name' => 'Online G2 - Mon/Wed 16:00 - A',
                'max_capacity' => 25,
                'days' => ['monday', 'wednesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(4)->format('Y-m-d'),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'location' => null,
                'location_type' => 'online',
                'location_map_url' => null,
                'is_active' => true,
            ],
            [
                'course_id' => $webCourse?->id,
                'grade_id' => $grade2?->id,
                'name' => 'Online G2 - Mon/Wed 16:00 - B',
                'max_capacity' => 25,
                'days' => ['monday', 'wednesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(4)->format('Y-m-d'),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'location' => null,
                'location_type' => 'online',
                'location_map_url' => null,
                'is_active' => true,
            ],

            // ========== OFFLINE GROUPS - Grade 2 ==========
            [
                'course_id' => $webCourse?->id,
                'grade_id' => $grade2?->id,
                'name' => 'Offline G2 - Alexandria - Mon/Wed 16:00 - A',
                'max_capacity' => 20,
                'days' => ['monday', 'wednesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(4)->format('Y-m-d'),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'location' => 'Alexandria Branch',
                'location_type' => 'offline',
                'location_map_url' => 'https://maps.google.com/?q=alexandria-branch',
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            if ($group['course_id'] && $group['grade_id']) {
                Group::create($group);
            }
        }

        $this->command->info('✅ Groups seeded successfully!');
    }
}
