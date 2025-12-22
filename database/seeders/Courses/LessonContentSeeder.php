<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Lesson;
use App\Features\Courses\Models\LessonContent;
use App\Features\Courses\Models\VideoContent;
use App\Features\Courses\Models\LiveSession;
use App\Features\Courses\Models\Quiz;
use App\Features\Courses\Models\Assignment;
use App\Features\Courses\Models\Material;
use Illuminate\Database\Seeder;

class LessonContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $welcomeLesson = Lesson::where('title', 'Welcome to the Course')->first();
        $setupLesson = Lesson::where('title', 'Setting Up Your Environment')->first();
        $variablesLesson = Lesson::where('title', 'Variables and Data Types')->first();
        $loopsLesson = Lesson::where('title', 'Loops')->first();
        $htmlLesson = Lesson::where('title', 'Introduction to HTML')->first();
        $selectLesson = Lesson::where('title', 'SELECT Queries')->first();
        $classesLesson = Lesson::where('title', 'Classes and Objects')->first();

        $contents = [
            // Welcome lesson contents
            [
                'lesson_id' => $welcomeLesson?->id,
                'type' => 'video',
                'title' => 'Course Introduction Video',
                'description' => 'Welcome video explaining course structure',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=intro1',
                    'video_provider' => 'youtube',
                    'duration' => 600,
                    'thumbnail_url' => 'https://example.com/thumb1.jpg',
                ],
            ],
            [
                'lesson_id' => $welcomeLesson?->id,
                'type' => 'material',
                'title' => 'Course Syllabus',
                'description' => 'Download the course syllabus',
                'order' => 2,
                'is_required' => false,
                'is_published' => true,
                'data' => [
                    'file_url' => 'https://example.com/syllabus.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 1024000,
                    'is_downloadable' => true,
                ],
            ],

            // Setup lesson contents
            [
                'lesson_id' => $setupLesson?->id,
                'type' => 'video',
                'title' => 'IDE Installation Guide',
                'description' => 'Step by step IDE setup',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=setup1',
                    'video_provider' => 'youtube',
                    'duration' => 900,
                    'thumbnail_url' => 'https://example.com/thumb2.jpg',
                ],
            ],
            [
                'lesson_id' => $setupLesson?->id,
                'type' => 'assignment',
                'title' => 'Environment Setup Verification',
                'description' => 'Verify your development environment is working',
                'order' => 2,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'instructions' => 'Take a screenshot of your IDE with a Hello World program running.',
                    'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
                    'max_score' => 10,
                    'allow_late_submission' => true,
                    'allowed_file_types' => ['png', 'jpg', 'jpeg'],
                    'max_file_size' => 5,
                ],
            ],

            // Variables lesson contents
            [
                'lesson_id' => $variablesLesson?->id,
                'type' => 'video',
                'title' => 'Variables Explained',
                'description' => 'Learn about variables and data types',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=vars1',
                    'video_provider' => 'youtube',
                    'duration' => 1200,
                    'thumbnail_url' => 'https://example.com/thumb3.jpg',
                ],
            ],
            [
                'lesson_id' => $variablesLesson?->id,
                'type' => 'quiz',
                'title' => 'Variables Quiz',
                'description' => 'Test your knowledge of variables',
                'order' => 2,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'time_limit' => 15,
                    'passing_score' => 70,
                    'max_attempts' => 3,
                    'shuffle_questions' => true,
                    'show_answers' => true,
                ],
            ],

            // Loops lesson contents
            [
                'lesson_id' => $loopsLesson?->id,
                'type' => 'video',
                'title' => 'Loops Tutorial',
                'description' => 'Learn about for and while loops',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=loops1',
                    'video_provider' => 'youtube',
                    'duration' => 1500,
                    'thumbnail_url' => 'https://example.com/thumb4.jpg',
                ],
            ],
            [
                'lesson_id' => $loopsLesson?->id,
                'type' => 'live_session',
                'title' => 'Live Q&A Session',
                'description' => 'Ask questions about loops',
                'order' => 2,
                'is_required' => false,
                'is_published' => true,
                'data' => [
                    'meeting_url' => 'https://zoom.us/j/1234567890',
                    'meeting_provider' => 'zoom',
                    'start_time' => now()->addDays(3)->setHour(14)->format('Y-m-d H:i:s'),
                    'end_time' => now()->addDays(3)->setHour(15)->format('Y-m-d H:i:s'),
                    'max_participants' => 50,
                ],
            ],

            // HTML lesson contents
            [
                'lesson_id' => $htmlLesson?->id,
                'type' => 'video',
                'title' => 'HTML Basics',
                'description' => 'Learn HTML fundamentals',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=html1',
                    'video_provider' => 'youtube',
                    'duration' => 1800,
                    'thumbnail_url' => 'https://example.com/html-thumb.jpg',
                ],
            ],
            [
                'lesson_id' => $htmlLesson?->id,
                'type' => 'material',
                'title' => 'HTML Cheat Sheet',
                'description' => 'Quick reference for HTML tags',
                'order' => 2,
                'is_required' => false,
                'is_published' => true,
                'data' => [
                    'file_url' => 'https://example.com/html-cheatsheet.pdf',
                    'file_type' => 'pdf',
                    'file_size' => 512000,
                    'is_downloadable' => true,
                ],
            ],

            // SQL lesson contents
            [
                'lesson_id' => $selectLesson?->id,
                'type' => 'video',
                'title' => 'SELECT Statement Tutorial',
                'description' => 'Learn how to query data',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=sql1',
                    'video_provider' => 'youtube',
                    'duration' => 1400,
                    'thumbnail_url' => 'https://example.com/sql-thumb.jpg',
                ],
            ],
            [
                'lesson_id' => $selectLesson?->id,
                'type' => 'quiz',
                'title' => 'SQL SELECT Quiz',
                'description' => 'Test your SQL knowledge',
                'order' => 2,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'time_limit' => 20,
                    'passing_score' => 60,
                    'max_attempts' => 2,
                    'shuffle_questions' => true,
                    'show_answers' => false,
                ],
            ],

            // OOP lesson contents
            [
                'lesson_id' => $classesLesson?->id,
                'type' => 'video',
                'title' => 'OOP Introduction',
                'description' => 'Learn about classes and objects',
                'order' => 1,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'video_url' => 'https://www.youtube.com/watch?v=oop1',
                    'video_provider' => 'youtube',
                    'duration' => 2400,
                    'thumbnail_url' => 'https://example.com/oop-thumb.jpg',
                ],
            ],
            [
                'lesson_id' => $classesLesson?->id,
                'type' => 'assignment',
                'title' => 'Create a Class',
                'description' => 'Build your first class',
                'order' => 2,
                'is_required' => true,
                'is_published' => true,
                'data' => [
                    'instructions' => 'Create a class representing a Car with properties and methods.',
                    'due_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
                    'max_score' => 100,
                    'allow_late_submission' => false,
                    'allowed_file_types' => ['php', 'zip'],
                    'max_file_size' => 10,
                ],
            ],
        ];

        foreach ($contents as $contentData) {
            if ($contentData['lesson_id']) {
                $this->createLessonContent($contentData);
            }
        }

        $this->command->info('✅ Lesson contents seeded successfully!');
    }

    protected function createLessonContent(array $contentData): void
    {
        $type = $contentData['type'];
        $data = $contentData['data'];

        // Create the polymorphic content
        $contentable = match ($type) {
            'video' => VideoContent::create($data),
            'live_session' => LiveSession::create($data),
            'quiz' => Quiz::create($data),
            'assignment' => Assignment::create($data),
            'material' => Material::create($data),
            default => throw new \InvalidArgumentException("Invalid content type: {$type}"),
        };

        // Create the lesson content
        LessonContent::create([
            'lesson_id' => $contentData['lesson_id'],
            'content_type' => $type,
            'contentable_type' => get_class($contentable),
            'contentable_id' => $contentable->id,
            'title' => $contentData['title'],
            'description' => $contentData['description'] ?? null,
            'order' => $contentData['order'] ?? 0,
            'duration' => $contentData['duration'] ?? 0,
            'is_required' => $contentData['is_required'] ?? false,
            'is_published' => $contentData['is_published'] ?? false,
        ]);
    }
}
