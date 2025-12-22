<?php

namespace Database\Seeders\Groups;

use App\Features\Groups\Models\Group;
use App\Features\Groups\Models\GroupSession;
use App\Features\Groups\Models\GroupStudent;
use App\Features\Groups\Models\Attendance;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = GroupSession::where('session_date', '<=', now())->get();

        foreach ($sessions as $session) {
            $groupStudents = GroupStudent::where('group_id', $session->group_id)
                ->where('status', 'active')
                ->get();

            foreach ($groupStudents as $groupStudent) {
                // Random attendance status with weighted probability
                $rand = rand(1, 100);
                if ($rand <= 75) {
                    $status = 'present';
                } elseif ($rand <= 90) {
                    $status = 'absent';
                } elseif ($rand <= 97) {
                    $status = 'late';
                } else {
                    $status = 'excused';
                }

                Attendance::create([
                    'group_id' => $session->group_id,
                    'session_id' => $session->id,
                    'student_id' => $groupStudent->student_id,
                    'status' => $status,
                    'attended_at' => in_array($status, ['present', 'late']) ? $session->session_date : null,
                    'notes' => $status === 'excused' ? 'Medical leave' : null,
                ]);
            }
        }

        $this->command->info('✅ Attendance records seeded successfully!');
    }
}
