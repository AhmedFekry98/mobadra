<?php

namespace Database\Seeders\Competitions;

use App\Features\Competitions\Models\Competition;
use App\Features\Competitions\Models\CompetitionLevel;
use Illuminate\Database\Seeder;

class CompetitionLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competition = Competition::first();

        if (!$competition) {
            $this->command->warn('⚠️ No competition found. Please seed competitions first.');
            return;
        }

        $levels = [
            [
                'competition_id' => $competition->id,
                'name' => 'Level One',
                'description' => 'First level of the competition',
                'level_order' => 1,
                'capacity' => 100,
            ],
            [
                'competition_id' => $competition->id,
                'name' => 'Level Two',
                'description' => 'Second level of the competition',
                'level_order' => 2,
                'capacity' => 50,
            ],
            [
                'competition_id' => $competition->id,
                'name' => 'Level Three',
                'description' => 'Third level of the competition',
                'level_order' => 3,
                'capacity' => 25,
            ],
            [
                'competition_id' => $competition->id,
                'name' => 'Hackathon',
                'description' => 'Hackathon level of the competition',
                'level_order' => 4,
                'capacity' => 20,
            ],
        ];

        foreach ($levels as $level) {
            CompetitionLevel::create($level);
        }

        $this->command->info('✅ Competition levels seeded successfully!');
    }
}
