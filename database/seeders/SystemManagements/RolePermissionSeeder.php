<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\Permission;
use App\Features\SystemManagements\Models\Role;
use App\Features\SystemManagements\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = __DIR__ . '/jsons/';

        // Map role names to their JSON permission files
        $rolePermissionFiles = [
            'admin' => 'adminsPermissions.json',
            'student' => 'StudentPermissions.json',
            'teacher' => 'TeacherPermissions.json',
        ];

        foreach ($rolePermissionFiles as $roleName => $fileName) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) {
                $this->command->warn("⚠️ Role '{$roleName}' not found, skipping...");
                continue;
            }

            $filePath = $jsonPath . $fileName;
            if (!file_exists($filePath)) {
                $this->command->warn("⚠️ Permission file not found: {$fileName}");
                continue;
            }

            $permissionGroups = json_decode(file_get_contents($filePath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->command->error("❌ Invalid JSON in file: {$fileName}");
                continue;
            }

            foreach ($permissionGroups as $group) {
                foreach ($group['items'] as $item) {
                    $permission = Permission::where('name', $item['name'])->first();
                    if ($permission) {
                        RolePermission::firstOrCreate([
                            'role_id' => $role->id,
                            'permission_id' => $permission->id,
                        ]);
                    }
                }
            }

            $this->command->info("✅ Permissions assigned to '{$roleName}' role successfully!");
        }
    }
}
