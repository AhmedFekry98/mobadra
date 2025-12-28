<?php

namespace Database\Seeders\SystemManagements;

use App\Features\SystemManagements\Models\Permission;
use App\Features\SystemManagements\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {

            foreach (Permission::all() as $permission) {
                RolePermission::firstOrCreate([
                    'role_id' => 1, // Admin
                    'permission_id' => $permission->id,
                ]);
            }
        } else {


            foreach (Permission::all() as $permission) {
                RolePermission::firstOrCreate([
                    'role_id' => 1, // Admin
                    'permission_id' => $permission->id,
                ]);
            }

        }

    }
}
