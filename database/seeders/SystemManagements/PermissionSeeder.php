<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = __DIR__ . '/jsons/';

        $permissionFiles = [
            'adminsPermissions.json',
            'TeacherPermissions.json',
            'StudentPermissions.json',
        ];

        foreach ($permissionFiles as $file) {
            $filePath = $jsonPath . $file;

            if (!file_exists($filePath)) {
                $this->command->warn("⚠️ Permission file not found: {$file}");
                continue;
            }

            $permissions = json_decode(file_get_contents($filePath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->command->error("❌ Invalid JSON in file: {$file}");
                continue;
            }

            foreach ($permissions as $group) {
                foreach ($group['items'] as $item) {
                    Permission::updateOrCreate(
                        ['name' => $item['name']],
                        [
                            'caption' => $item['caption'],
                            'group' => $group['group'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            $this->command->info("✅ Permissions from {$file} seeded successfully!");
        }

        $this->command->info('✅ All permissions seeded successfully!');


    }
}
