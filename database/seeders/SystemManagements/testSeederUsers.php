<?php

namespace Database\Seeders\SystemManagements;

use App\Enums\Gender;
use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Models\UserInformation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestSeederUsers extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = [
            'G4.1@3cschool.net',
            'G4.2@3cschool.net',
            'G4.3@3cschool.net',
            'G5.1@3cschool.net',
            'G5.2@3cschool.net',
            'G5.3@3cschool.net',
            'G6.1@3cschool.net',
            'G6.2@3cschool.net',
            'G6.3@3cschool.net',
        ];

        $studentRole = Role::where('name', 'student')->first();

        foreach ($emails as $email) {
            $name = explode('@', $email)[0];

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('demi@3c'),
                'role_id' => $studentRole?->id,
            ]);

            UserInformation::create([
                'user_id' => $user->id,
                'gender' => Gender::MALE,
                'phone_code' => '+20',
                'phone_number' => '01000000000',
                'nationality' => 'Egyptian',
            ]);
        }
    }
}


