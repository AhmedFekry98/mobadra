<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Term;
use App\Features\Courses\Models\Course;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $firstTerm = Term::where('name', 'First Term 2024-2025')->first();
        $secondTerm = Term::where('name', 'Second Term 2024-2025')->first();

        $courses = [
            [
                'term_id' => $firstTerm?->id,
                'title' => 'Introduction to Programming',
                'description' => 'Learn the fundamentals of programming with practical examples',
                'slug' => 'intro-to-programming',
                'is_active' => true,
            ],
            [
                'term_id' => $firstTerm?->id,
                'title' => 'Web Development Basics',
                'description' => 'Learn HTML, CSS, and JavaScript fundamentals',
                'slug' => 'web-dev-basics',
                'is_active' => true,
            ],
            [
                'term_id' => $firstTerm?->id,
                'title' => 'Database Fundamentals',
                'description' => 'Learn SQL and database design principles',
                'slug' => 'database-fundamentals',
                'is_active' => true,
            ],
            [
                'term_id' => $secondTerm?->id,
                'title' => 'Advanced Programming',
                'description' => 'Advanced programming concepts and design patterns',
                'slug' => 'advanced-programming',
                'is_active' => false,
            ],
            [
                'term_id' => $secondTerm?->id,
                'title' => 'API Development',
                'description' => 'Build RESTful APIs with Laravel',
                'slug' => 'api-development',
                'is_active' => false,
            ],
        ];

        foreach ($courses as $course) {
            if ($course['term_id']) {
                Course::create($course);
            }
        }

        $this->command->info('✅ Courses seeded successfully!');
    }
}
