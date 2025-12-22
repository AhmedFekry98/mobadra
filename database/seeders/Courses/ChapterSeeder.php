<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $introCourse = Course::where('slug', 'intro-to-programming')->first();
        $webCourse = Course::where('slug', 'web-dev-basics')->first();
        $dbCourse = Course::where('slug', 'database-fundamentals')->first();
        $advancedCourse = Course::where('slug', 'advanced-programming')->first();

        $chapters = [
            // Introduction to Programming chapters
            [
                'course_id' => $introCourse?->id,
                'title' => 'Getting Started',
                'description' => 'Introduction and environment setup',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'title' => 'Basic Concepts',
                'description' => 'Variables, data types, and operators',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $introCourse?->id,
                'title' => 'Control Flow',
                'description' => 'Conditionals and loops',
                'order' => 3,
                'is_active' => true,
            ],

            // Web Development chapters
            [
                'course_id' => $webCourse?->id,
                'title' => 'HTML Fundamentals',
                'description' => 'Learn the structure of web pages',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $webCourse?->id,
                'title' => 'CSS Styling',
                'description' => 'Style your web pages',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $webCourse?->id,
                'title' => 'JavaScript Basics',
                'description' => 'Add interactivity to your pages',
                'order' => 3,
                'is_active' => true,
            ],

            // Database chapters
            [
                'course_id' => $dbCourse?->id,
                'title' => 'Introduction to Databases',
                'description' => 'What are databases and why use them',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $dbCourse?->id,
                'title' => 'SQL Basics',
                'description' => 'Learn SQL queries',
                'order' => 2,
                'is_active' => true,
            ],

            // Advanced Programming chapters
            [
                'course_id' => $advancedCourse?->id,
                'title' => 'Object-Oriented Programming',
                'description' => 'Learn OOP principles',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $advancedCourse?->id,
                'title' => 'Design Patterns',
                'description' => 'Common software design patterns',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($chapters as $chapter) {
            if ($chapter['course_id']) {
                Chapter::create($chapter);
            }
        }

        $this->command->info('✅ Chapters seeded successfully!');
    }
}
