<?php

namespace Database\Seeders\Groups;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupTeacher;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Seeder;

class GroupTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = Group::all();
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($teachers->isEmpty()) {
            $teachers = User::take(3)->get();
        }

        foreach ($groups as $index => $group) {
            // Assign primary teacher
            $primaryTeacher = $teachers->get($index % $teachers->count());
            if ($primaryTeacher) {
                GroupTeacher::create([
                    'group_id' => $group->id,
                    'teacher_id' => $primaryTeacher->id,
                    'assigned_at' => now()->subDays(rand(1, 30)),
                    'is_primary' => true,
                ]);
            }

            // Optionally assign assistant teacher
            if (rand(0, 1) && $teachers->count() > 1) {
                $assistantTeacher = $teachers->except($primaryTeacher->id)->random();
                GroupTeacher::create([
                    'group_id' => $group->id,
                    'teacher_id' => $assistantTeacher->id,
                    'assigned_at' => now()->subDays(rand(1, 20)),
                    'is_primary' => false,
                ]);
            }
        }

        $this->command->info('✅ Group teachers seeded successfully!');
    }
}
