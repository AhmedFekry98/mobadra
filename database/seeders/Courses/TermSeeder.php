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
                'name' => 'First Term',
                'description' => 'First academic term',
                'is_active' => true,
            ],
            [
                'name' => 'Second Term',
                'description' => 'Second academic term',
                'is_active' => true,
            ],
            [
                'name' => 'Summer Term',
                'description' => 'Summer academic term',
                'is_active' => true,
            ],
        ];

        foreach ($terms as $term) {
            Term::create($term);
        }

        $this->command->info('✅ Terms seeded successfully!');
    }
}
