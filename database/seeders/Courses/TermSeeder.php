<?php

namespace Database\Seeders\Courses;

use App\Features\Courses\Models\Term;
use Illuminate\Database\Seeder;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            [
                'name' => 'First Term 2024-2025',
                'description' => 'First academic term for the year 2024-2025',
                'is_active' => true,
            ],
            [
                'name' => 'Second Term 2024-2025',
                'description' => 'Second academic term for the year 2024-2025',
                'is_active' => false,
            ],
            [
                'name' => 'Summer Term 2025',
                'description' => 'Summer academic term for the year 2025',
                'is_active' => false,
            ],
        ];

        foreach ($terms as $term) {
            Term::create($term);
        }

        $this->command->info('✅ Terms seeded successfully!');
    }
}
