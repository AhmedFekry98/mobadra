<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = app()->environment('production')
        ? [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'role_id' => 1,
                'password' => Hash::make('password'),
            ],
        ]
        : [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'role_id' => 1,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Student User',
                'email' => 'student@example.com',
                'role_id' => 2,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Teacher User',
                'email' => 'teacher@example.com',
                'role_id' => 3,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Parent User',
                'email' => 'parent@example.com',
                'role_id' => 4,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
