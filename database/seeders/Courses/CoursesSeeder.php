<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Term;
use App\Features\Courses\Models\Course;
use App\Features\Grades\Models\Grade;
use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $courses = [
            // grade 4 term 1
            [
                'term_id' => Term::where('name','First Term')->first()->id,
                'title' => 'Introduction to computer and programming Course',
                'description' => 'Introduction to computer and programming Course for grade 4',
                'slug' => 'intro-to-programming',
                'grade_id' => Grade::where('name','Grade 4')->first()->id,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        $this->command->info('✅ Courses seeded successfully!');
    }
}
