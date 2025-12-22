<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Courses\ChapterSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\SystemManagements\UserSeeder;
use Database\Seeders\SystemManagements\RoleSeeder;
use Database\Seeders\SystemManagements\PermissionSeeder;
use Database\Seeders\SystemManagements\RolePermissionSeeder;
use Database\Seeders\Courses\CourseSeeder;
use Database\Seeders\Courses\CoursesSeeder;
use Database\Seeders\Courses\LessonContentSeeder;
use Database\Seeders\Courses\LessonSeeder;
use Database\Seeders\Courses\TermSeeder;
use Database\Seeders\SystemManagements\GeneralSettingSeeder;
use Database\Seeders\SystemManagements\AuditSeeder;
use Database\Seeders\SystemManagements\FAQSeeder;
use Database\Seeders\SystemManagements\UserInformationSeeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Development seeders
     */
    private $developmentSeeders = [
        RoleSeeder::class,
        UserSeeder::class,
        PermissionSeeder::class,
        RolePermissionSeeder::class,
        TermSeeder::class,
        CoursesSeeder::class,
        ChapterSeeder::class,
        LessonSeeder::class,
        LessonContentSeeder::class,
        GeneralSettingSeeder::class,
        AuditSeeder::class,
        FAQSeeder::class,
        UserInformationSeeder::class,
    ];

    /**
     * Production seeders
     */
    private $productionSeeders = [
        RoleSeeder::class,
        UserSeeder::class,
        PermissionSeeder::class,
        RolePermissionSeeder::class,
        GeneralSettingSeeder::class,
        TermSeeder::class,
    ];




    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // make sure to run variable seeders based on environment
        if (! app()->environment('production')) {
            $this->call($this->developmentSeeders);
        }else{
            $this->call($this->productionSeeders);
        }

    }
}
