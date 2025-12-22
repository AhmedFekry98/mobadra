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
        $permissions = [
            // General Dashboard
            [
                'group' => 'General Dashboard',
                'items' => [
                    ['name' => 'dashboard_view', 'caption' => 'Show Dashboard'],
                    ['name' => 'dashboard_stats', 'caption' => 'Show Dashboard Stats'],
                ],
            ],

            // Users
            [
                'group' => 'Users',
                'items' => [
                    ['name' => 'user.viewAny', 'caption' => 'Show Users'],
                    ['name' => 'user.view', 'caption' => 'View Single User'],
                    ['name' => 'user.create', 'caption' => 'Create New User'],
                    ['name' => 'user.update', 'caption' => 'Update User'],
                    ['name' => 'user.delete', 'caption' => 'Delete User'],
                    ['name' => 'user.restore', 'caption' => 'Restore User'],
                    ['name' => 'user.forceDelete', 'caption' => 'Force Delete User'],
                ],
            ],

            // Roles
            [
                'group' => 'Roles',
                'items' => [
                    ['name' => 'role.viewAny', 'caption' => 'Show Roles'],
                    ['name' => 'role.view', 'caption' => 'View Single Role'],
                    ['name' => 'role.create', 'caption' => 'Create New Role'],
                    ['name' => 'role.update', 'caption' => 'Update Role'],
                    ['name' => 'role.delete', 'caption' => 'Delete Role'],
                    ['name' => 'role.restore', 'caption' => 'Restore Role'],
                    ['name' => 'role.forceDelete', 'caption' => 'Force Delete Role'],
                ],
            ],

            // Permissions
            [
                'group' => 'Permissions',
                'items' => [
                    ['name' => 'permission.viewAny', 'caption' => 'Show Permissions'],
                    ['name' => 'permission.view', 'caption' => 'View Single Permission'],
                    ['name' => 'permission.create', 'caption' => 'Create New Permission'],
                    ['name' => 'permission.update', 'caption' => 'Update Permission'],
                    ['name' => 'permission.delete', 'caption' => 'Delete Permission'],
                    ['name' => 'permission.restore', 'caption' => 'Restore Permission'],
                    ['name' => 'permission.forceDelete', 'caption' => 'Force Delete Permission'],
                ],
            ],

            // User Roles
            [
                'group' => 'User Roles',
                'items' => [
                    ['name' => 'user_role.viewAny', 'caption' => 'Show User Roles'],
                    ['name' => 'user_role.view', 'caption' => 'View User Role'],
                    ['name' => 'user_role.create', 'caption' => 'Assign Role to User'],
                    ['name' => 'user_role.update', 'caption' => 'Update User Role'],
                    ['name' => 'user_role.delete', 'caption' => 'Remove Role from User'],
                ],
            ],

            // Role Permissions
            [
                'group' => 'Role Permissions',
                'items' => [
                    ['name' => 'role_permission.viewAny', 'caption' => 'Show Role Permissions'],
                    ['name' => 'role_permission.view', 'caption' => 'View Role Permission'],
                    ['name' => 'role_permission.create', 'caption' => 'Assign Permission to Role'],
                    ['name' => 'role_permission.update', 'caption' => 'Update Role Permission'],
                    ['name' => 'role_permission.delete', 'caption' => 'Remove Permission from Role'],
                ],
            ],

            // Staff Management
            [
                'group' => 'Staff Management',
                'items' => [
                    ['name' => 'staff.viewAny', 'caption' => 'Show Staff Members'],
                    ['name' => 'staff.view', 'caption' => 'View Single Staff Member'],
                    ['name' => 'staff.create', 'caption' => 'Create New Staff Member'],
                    ['name' => 'staff.update', 'caption' => 'Update Staff Member'],
                    ['name' => 'staff.delete', 'caption' => 'Delete Staff Member'],
                    ['name' => 'staff.restore', 'caption' => 'Restore Staff Member'],
                    ['name' => 'staff.forceDelete', 'caption' => 'Force Delete Staff Member'],
                ],
            ],

            // FAQs
            [
                'group' => 'FAQs',
                'items' => [
                    ['name' => 'faq.viewAny', 'caption' => 'Show FAQs'],
                    ['name' => 'faq.view', 'caption' => 'View Single FAQ'],
                    ['name' => 'faq.create', 'caption' => 'Create New FAQ'],
                    ['name' => 'faq.update', 'caption' => 'Update FAQ'],
                    ['name' => 'faq.delete', 'caption' => 'Delete FAQ'],
                    ['name' => 'faq.restore', 'caption' => 'Restore FAQ'],
                    ['name' => 'faq.forceDelete', 'caption' => 'Force Delete FAQ'],
                ],
            ],

            // Audits
            [
                'group' => 'Audits',
                'items' => [
                    ['name' => 'audit.viewAny', 'caption' => 'Show Audits'],
                    ['name' => 'audit.view', 'caption' => 'View Single Audit'],
                    ['name' => 'audit.create', 'caption' => 'Create New Audit'],
                    ['name' => 'audit.update', 'caption' => 'Update Audit'],
                    ['name' => 'audit.delete', 'caption' => 'Delete Audit'],
                    ['name' => 'audit.restore', 'caption' => 'Restore Audit'],
                    ['name' => 'audit.forceDelete', 'caption' => 'Force Delete Audit'],
                    ['name' => 'audit.securityAlerts', 'caption' => 'Security Alerts'],
                    ['name' => 'audit.cleanup', 'caption' => 'Cleanup'],
                ],
            ],

            // Terms
            [
                'group' => 'Terms',
                'items' => [
                    ['name' => 'term.viewAny', 'caption' => 'Show Terms'],
                    ['name' => 'term.view', 'caption' => 'View Single Term'],
                    ['name' => 'term.create', 'caption' => 'Create New Term'],
                    ['name' => 'term.update', 'caption' => 'Update Term'],
                    ['name' => 'term.delete', 'caption' => 'Delete Term'],
                    ['name' => 'term.restore', 'caption' => 'Restore Term'],
                    ['name' => 'term.forceDelete', 'caption' => 'Force Delete Term'],
                ],
            ],

            // Courses
            [
                'group' => 'Courses',
                'items' => [
                    ['name' => 'course.viewAny', 'caption' => 'Show Courses'],
                    ['name' => 'course.view', 'caption' => 'View Single Course'],
                    ['name' => 'course.create', 'caption' => 'Create New Course'],
                    ['name' => 'course.update', 'caption' => 'Update Course'],
                    ['name' => 'course.delete', 'caption' => 'Delete Course'],
                    ['name' => 'course.restore', 'caption' => 'Restore Course'],
                    ['name' => 'course.forceDelete', 'caption' => 'Force Delete Course'],
                ],
            ],

            // Chapters
            [
                'group' => 'Chapters',
                'items' => [
                    ['name' => 'chapter.viewAny', 'caption' => 'Show Chapters'],
                    ['name' => 'chapter.view', 'caption' => 'View Single Chapter'],
                    ['name' => 'chapter.create', 'caption' => 'Create New Chapter'],
                    ['name' => 'chapter.update', 'caption' => 'Update Chapter'],
                    ['name' => 'chapter.delete', 'caption' => 'Delete Chapter'],
                    ['name' => 'chapter.restore', 'caption' => 'Restore Chapter'],
                    ['name' => 'chapter.forceDelete', 'caption' => 'Force Delete Chapter'],
                ],
            ],

            // Lessons
            [
                'group' => 'Lessons',
                'items' => [
                    ['name' => 'lesson.viewAny', 'caption' => 'Show Lessons'],
                    ['name' => 'lesson.view', 'caption' => 'View Single Lesson'],
                    ['name' => 'lesson.create', 'caption' => 'Create New Lesson'],
                    ['name' => 'lesson.update', 'caption' => 'Update Lesson'],
                    ['name' => 'lesson.delete', 'caption' => 'Delete Lesson'],
                    ['name' => 'lesson.restore', 'caption' => 'Restore Lesson'],
                    ['name' => 'lesson.forceDelete', 'caption' => 'Force Delete Lesson'],
                ],
            ],

            // Lesson Contents
            [
                'group' => 'Lesson Contents',
                'items' => [
                    ['name' => 'lesson_content.viewAny', 'caption' => 'Show Lesson Contents'],
                    ['name' => 'lesson_content.view', 'caption' => 'View Single Lesson Content'],
                    ['name' => 'lesson_content.create', 'caption' => 'Create New Lesson Content'],
                    ['name' => 'lesson_content.update', 'caption' => 'Update Lesson Content'],
                    ['name' => 'lesson_content.delete', 'caption' => 'Delete Lesson Content'],
                    ['name' => 'lesson_content.restore', 'caption' => 'Restore Lesson Content'],
                    ['name' => 'lesson_content.forceDelete', 'caption' => 'Force Delete Lesson Content'],
                ],
            ],
        ];

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

        $this->command->info('✅ Permissions seeded successfully!');


    }
}
