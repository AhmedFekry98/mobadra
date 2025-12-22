<?php

namespace Database\Seeders\Groups;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupStudent;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Seeder;

class GroupStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = Group::all();
        $students = User::whereHas('role', function ($query) {
            $query->where('name', 'customer');
        })->get();

        if ($students->isEmpty()) {
            $students = User::take(10)->get();
        }

        foreach ($groups as $group) {
            // Add random students to each group (5-15 students)
            $randomStudents = $students->random(min($students->count(), rand(5, 15)));

            foreach ($randomStudents as $student) {
                GroupStudent::create([
                    'group_id' => $group->id,
                    'student_id' => $student->id,
                    'enrolled_at' => now()->subDays(rand(1, 30)),
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('✅ Group students seeded successfully!');
    }
}
