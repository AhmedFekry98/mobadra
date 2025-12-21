<?php

namespace Database\Seeders\SystemManagements;

use App\Enums\Gender;
use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->userInformation()->create([
                'phone_code' => fake()->countryCode(),
                'phone_number' => fake()->phoneNumber(),
                'date_of_birth' => fake()->date(),
            'gender' => fake()->randomElement(\App\Enums\Gender::cases()),
                'nationality' => fake()->country(),
                'address' => fake()->address(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'country' => fake()->country(),
                'postal_code' => fake()->postcode(),
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => fake()->phoneNumber(),
                'bio' => fake()->sentence(),
                'social_links' => json_encode([
                    'facebook' => 'https://facebook.com',
                    'twitter' => 'https://twitter.com',
                    'linkedin' => 'https://linkedin.com',
                    'instagram' => 'https://instagram.com',
                ]),
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }
    }
}
