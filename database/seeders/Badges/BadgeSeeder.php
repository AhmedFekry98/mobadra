<?php

namespace Database\Seeders\Badges;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Features\Badges\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 1; $i <= 10; $i++){
            Badge::create([
                'name' => fake()->name(),
                'type' => fake()->randomElement(['Certification', 'Achievement','Behavior']),
                'description' => fake()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
