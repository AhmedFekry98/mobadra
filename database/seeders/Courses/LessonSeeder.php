<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Course;
use App\Features\Courses\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programming = Course::where('title', 'Introduction to programming')->first();
        $webDevCourse = Course::where('title', 'Web Development Basics')->first();
        $dataScience = Course::where('title', 'Data Science with Python')->first();

        $lessons = [
            // Python Course lessons
            [
                'course_id' => $programming?->id,
                'title' => 'Welcome to Python',
                'description' => 'Course overview and objectives',
                'lesson_type' => 'online',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Setting Up Your Environment',
                'description' => 'Install and configure Python',
                'lesson_type' => 'online',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Variables and Data Types',
                'description' => 'Understanding variables and data types',
                'lesson_type' => 'online',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Control Flow',
                'description' => 'If statements and loops',
                'lesson_type' => 'online',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Functions',
                'description' => 'Creating and using functions',
                'lesson_type' => 'online',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Object-Oriented programming',
                'description' => 'Classes and objects in Python',
                'lesson_type' => 'online',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'File Handling',
                'description' => 'Reading and writing files',
                'lesson_type' => 'online',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'course_id' => $programming?->id,
                'title' => 'Practice Session',
                'description' => 'Hands-on practice session',
                'lesson_type' => 'offline',
                'order' => 8,
                'is_active' => true,
            ],

            // Web Development Course lessons
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'Introduction to HTML',
                'description' => 'Basic HTML tags and structure',
                'lesson_type' => 'online',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'HTML Forms',
                'description' => 'Creating forms and inputs',
                'lesson_type' => 'online',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'CSS Basics',
                'description' => 'Styling web pages',
                'lesson_type' => 'online',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'CSS Flexbox',
                'description' => 'Layout with Flexbox',
                'lesson_type' => 'online',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'JavaScript Basics',
                'description' => 'Introduction to JavaScript',
                'lesson_type' => 'online',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'DOM Manipulation',
                'description' => 'Working with the DOM',
                'lesson_type' => 'online',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'Responsive Design',
                'description' => 'Making websites responsive',
                'lesson_type' => 'online',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'course_id' => $webDevCourse?->id,
                'title' => 'Project Workshop',
                'description' => 'Build a complete website',
                'lesson_type' => 'offline',
                'order' => 8,
                'is_active' => true,
            ],

            // Data Science Course lessons
            [
                'course_id' => $dataScience?->id,
                'title' => 'Introduction to Data Science',
                'description' => 'Overview of data science',
                'lesson_type' => 'online',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'NumPy Basics',
                'description' => 'Working with NumPy arrays',
                'lesson_type' => 'online',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Pandas DataFrames',
                'description' => 'Data manipulation with Pandas',
                'lesson_type' => 'online',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Data Visualization',
                'description' => 'Creating charts and graphs',
                'lesson_type' => 'online',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Machine Learning Intro',
                'description' => 'Introduction to ML concepts',
                'lesson_type' => 'online',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Supervised Learning',
                'description' => 'Classification and regression',
                'lesson_type' => 'online',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Model Evaluation',
                'description' => 'Evaluating ML models',
                'lesson_type' => 'online',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'course_id' => $dataScience?->id,
                'title' => 'Data Science Lab',
                'description' => 'Hands-on data analysis project',
                'lesson_type' => 'offline',
                'order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($lessons as $lesson) {
            if ($lesson['course_id']) {
                Lesson::create($lesson);
            }
        }

        $this->command->info('✅ Lessons seeded successfully!');
    }
}
