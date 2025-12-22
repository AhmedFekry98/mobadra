<?php

namespace Database\Seeders\Groups;

use App\Features\Courses\Models\Course;
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

        $groups = [
            // Introduction to Programming groups
            [
                'course_id' => $introCourse?->id,
                'name' => 'Group A - Friday/Saturday',
                'max_capacity' => 25,
                'days' => ['friday', 'saturday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => 'Room 101',
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'name' => 'Group B - Friday/Saturday',
                'max_capacity' => 25,
                'days' => ['friday', 'saturday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '14:00',
                'end_time' => '16:00',
                'location' => 'Room 102',
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'name' => 'Group C - Sunday/Tuesday',
                'max_capacity' => 20,
                'days' => ['sunday', 'tuesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(3)->format('Y-m-d'),
                'start_time' => '18:00',
                'end_time' => '20:00',
                'location' => 'Room 103',
                'is_active' => true,
            ],

            // Web Development groups
            [
                'course_id' => $webCourse?->id,
                'name' => 'Web Dev - Group A',
                'max_capacity' => 25,
                'days' => ['friday', 'saturday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(4)->format('Y-m-d'),
                'start_time' => '10:00',
                'end_time' => '12:00',
                'location' => 'Lab 1',
                'is_active' => true,
            ],
            [
                'course_id' => $webCourse?->id,
                'name' => 'Web Dev - Group B',
                'max_capacity' => 25,
                'days' => ['sunday', 'wednesday'],
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addMonths(4)->format('Y-m-d'),
                'start_time' => '16:00',
                'end_time' => '18:00',
                'location' => 'Lab 2',
                'is_active' => true,
            ],
        ];

        foreach ($groups as $group) {
            if ($group['course_id']) {
                Group::create($group);
            }
        }

        $this->command->info('✅ Groups seeded successfully!');
    }
}
