<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
            TeacherSeeder::class,
            SubjectSeeder::class,
            StaffSeeder::class,
            StudentSeeder::class,
            StaffModuleSeeder::class,
            ExamResultSeeder::class,
            FeesAccountsSeeder::class,
            TimetableSeeder::class,
            LibrarySeeder::class,
            TransportSeeder::class,
            NoticeSeeder::class,
        ]);
    }
}
