<?php

namespace Database\Seeders\Badges;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Features\Badges\Models\Badge;
use App\Features\Badges\Models\BadgeCondition;

class BadgeConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = Badge::all();
        foreach ($badges as $badge) {
            BadgeCondition::create([
                'badge_id' => $badge->id,
                'field' => match ($badge->type) {
                    'Certification' => 'certification_count',
                    'Achievement' => 'achievement_points',
                    'Behavior' => 'behavior_score',
                    default => 'points',
                },
                'operator' => fake()->randomElement(['=', '>', '<', '>=', '<=']),
                'value' => fake()->numberBetween(1, 100),
            ]);
        }
    }
}
