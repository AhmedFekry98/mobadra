<?php

namespace App\Features\Grades\Seeders;

use App\Features\Grades\Models\Grade;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            [
                'name' => 'Grade 1',
                'code' => 'G1',
                'description' => 'First grade for beginners',
                'min_age' => 6,
                'max_age' => 7,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 2',
                'code' => 'G2',
                'description' => 'Second grade',
                'min_age' => 7,
                'max_age' => 8,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 3',
                'code' => 'G3',
                'description' => 'Third grade',
                'min_age' => 8,
                'max_age' => 9,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 4',
                'code' => 'G4',
                'description' => 'Fourth grade',
                'min_age' => 9,
                'max_age' => 10,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 5',
                'code' => 'G5',
                'description' => 'Fifth grade',
                'min_age' => 10,
                'max_age' => 11,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 6',
                'code' => 'G6',
                'description' => 'Sixth grade',
                'min_age' => 11,
                'max_age' => 12,
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 7',
                'code' => 'G7',
                'description' => 'Seventh grade - Middle school',
                'min_age' => 12,
                'max_age' => 13,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 8',
                'code' => 'G8',
                'description' => 'Eighth grade - Middle school',
                'min_age' => 13,
                'max_age' => 14,
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 9',
                'code' => 'G9',
                'description' => 'Ninth grade - High school',
                'min_age' => 14,
                'max_age' => 15,
                'order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 10',
                'code' => 'G10',
                'description' => 'Tenth grade - High school',
                'min_age' => 15,
                'max_age' => 16,
                'order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 11',
                'code' => 'G11',
                'description' => 'Eleventh grade - High school',
                'min_age' => 16,
                'max_age' => 17,
                'order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Grade 12',
                'code' => 'G12',
                'description' => 'Twelfth grade - High school',
                'min_age' => 17,
                'max_age' => 18,
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($grades as $grade) {
            Grade::updateOrCreate(
                ['code' => $grade['code']],
                $grade
            );
        }
    }
}
