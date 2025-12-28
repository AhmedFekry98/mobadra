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


            $editPost = Permission::where('name', 'edit_posts')->first();
            $viewPost = Permission::where('name', 'grades.view')->first();
            $viewGroup = Permission::where('name', 'groups.viewAny')->first();
            $viewLessonContent = Permission::where('name', 'lesson_content.viewAny')->first();
            // add all permissions for ticket support
            $ticketPermissions = Permission::whereIn('name', [
                'support_tickets.view',
                'support_tickets.viewAny',
                'support_tickets.create',
            ])->get();

            $lesson = Permission::where('name', 'lesson.viewAny')->first();

            $course = Permission::where('name', 'course.viewAny')->first();

            if ($lesson) {
                RolePermission::firstOrCreate([
                    'role_id' => 2, // student
                    'permission_id' => $lesson->id,
                ]);
            }



            foreach ($ticketPermissions as $ticketPermission) {
                RolePermission::firstOrCreate([
                    'role_id' => 2, // student
                    'permission_id' => $ticketPermission->id,
                ]);
            }

            if ($viewGroup) {
                RolePermission::firstOrCreate([
                    'role_id' => 2, // student
                    'permission_id' => $viewGroup->id,
                ]);
            }


            if ($viewLessonContent) {
                RolePermission::firstOrCreate([
                    'role_id' => 2, // student
                    'permission_id' => $viewLessonContent->id,
                ]);
            }

            if ($course) {
                RolePermission::firstOrCreate([
                    'role_id' => 2, // student
                    'permission_id' => $course->id,
                ]);
            }

            if ($viewPost) {
                RolePermission::firstOrCreate([
                    'role_id' => 3, // parent
                    'permission_id' => $viewPost->id,
                ]);
            }
        }

    }
}
