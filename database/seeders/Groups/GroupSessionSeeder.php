<?php

namespace Database\Seeders\Groups;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupSession;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GroupSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = Group::all();

        foreach ($groups as $group) {
            $startDate = Carbon::parse($group->start_date);
            $endDate = Carbon::parse($group->end_date);
            $days = $group->days;

            $dayMap = [
                'sunday' => Carbon::SUNDAY,
                'monday' => Carbon::MONDAY,
                'tuesday' => Carbon::TUESDAY,
                'wednesday' => Carbon::WEDNESDAY,
                'thursday' => Carbon::THURSDAY,
                'friday' => Carbon::FRIDAY,
                'saturday' => Carbon::SATURDAY,
            ];

            $sessionNumber = 1;
            $currentDate = $startDate->copy();

            // Generate sessions for the first 4 weeks
            while ($currentDate->lte($startDate->copy()->addWeeks(4)) && $currentDate->lte($endDate)) {
                foreach ($days as $day) {
                    $dayOfWeek = $dayMap[$day] ?? null;
                    if ($dayOfWeek === null) continue;

                    $sessionDate = $currentDate->copy();
                    if ($sessionDate->dayOfWeek !== $dayOfWeek) {
                        $sessionDate = $sessionDate->next($dayOfWeek);
                    }

                    if ($sessionDate->lte($endDate) && $sessionDate->lte($startDate->copy()->addWeeks(4))) {
                        GroupSession::create([
                            'group_id' => $group->id,
                            'session_date' => $sessionDate->format('Y-m-d'),
                            'start_time' => $group->start_time,
                            'end_time' => $group->end_time,
                            'topic' => "Session {$sessionNumber}: " . $this->getSessionTopic($sessionNumber),
                            'is_cancelled' => false,
                        ]);
                        $sessionNumber++;
                    }
                }
                $currentDate->addWeek();
            }
        }

        $this->command->info('✅ Group sessions seeded successfully!');
    }

    protected function getSessionTopic(int $sessionNumber): string
    {
        $topics = [
            1 => 'Introduction & Overview',
            2 => 'Getting Started',
            3 => 'Basic Concepts',
            4 => 'Hands-on Practice',
            5 => 'Advanced Topics',
            6 => 'Project Work',
            7 => 'Review Session',
            8 => 'Q&A Session',
        ];

        return $topics[$sessionNumber] ?? "Topic {$sessionNumber}";
    }
}
