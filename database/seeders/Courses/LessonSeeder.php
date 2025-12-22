<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Chapter;
use App\Features\Courses\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gettingStarted = Chapter::where('title', 'Getting Started')->first();
        $basicConcepts = Chapter::where('title', 'Basic Concepts')->first();
        $controlFlow = Chapter::where('title', 'Control Flow')->first();
        $htmlFundamentals = Chapter::where('title', 'HTML Fundamentals')->first();
        $cssStyling = Chapter::where('title', 'CSS Styling')->first();
        $sqlBasics = Chapter::where('title', 'SQL Basics')->first();
        $oop = Chapter::where('title', 'Object-Oriented Programming')->first();

        $lessons = [
            // Getting Started lessons
            [
                'chapter_id' => $gettingStarted?->id,
                'title' => 'Welcome to the Course',
                'description' => 'Course overview and objectives',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $gettingStarted?->id,
                'title' => 'Setting Up Your Environment',
                'description' => 'Install and configure development tools',
                'order' => 2,
                'is_active' => true,
            ],

            // Basic Concepts lessons
            [
                'chapter_id' => $basicConcepts?->id,
                'title' => 'Variables and Data Types',
                'description' => 'Understanding variables and data types',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $basicConcepts?->id,
                'title' => 'Operators',
                'description' => 'Arithmetic and logical operators',
                'order' => 2,
                'is_active' => true,
            ],

            // Control Flow lessons
            [
                'chapter_id' => $controlFlow?->id,
                'title' => 'If Statements',
                'description' => 'Conditional logic with if/else',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $controlFlow?->id,
                'title' => 'Loops',
                'description' => 'For and while loops',
                'order' => 2,
                'is_active' => true,
            ],

            // HTML lessons
            [
                'chapter_id' => $htmlFundamentals?->id,
                'title' => 'Introduction to HTML',
                'description' => 'Basic HTML tags and structure',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $htmlFundamentals?->id,
                'title' => 'HTML Forms',
                'description' => 'Creating forms and inputs',
                'order' => 2,
                'is_active' => true,
            ],

            // CSS lessons
            [
                'chapter_id' => $cssStyling?->id,
                'title' => 'CSS Selectors',
                'description' => 'Selecting elements to style',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $cssStyling?->id,
                'title' => 'CSS Flexbox',
                'description' => 'Layout with Flexbox',
                'order' => 2,
                'is_active' => true,
            ],

            // SQL lessons
            [
                'chapter_id' => $sqlBasics?->id,
                'title' => 'SELECT Queries',
                'description' => 'Retrieving data from tables',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $sqlBasics?->id,
                'title' => 'INSERT, UPDATE, DELETE',
                'description' => 'Modifying data in tables',
                'order' => 2,
                'is_active' => true,
            ],

            // OOP lessons
            [
                'chapter_id' => $oop?->id,
                'title' => 'Classes and Objects',
                'description' => 'Understanding classes and objects',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'chapter_id' => $oop?->id,
                'title' => 'Inheritance',
                'description' => 'Extending classes',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($lessons as $lesson) {
            if ($lesson['chapter_id']) {
                Lesson::create($lesson);
            }
        }

        $this->command->info('✅ Lessons seeded successfully!');
    }
}
