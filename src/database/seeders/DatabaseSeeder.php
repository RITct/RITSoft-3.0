<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTableSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(ClassroomSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(FacultyUserSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(OfficeStaffSeeder::class);
        $this->call(CourseSeeder::class);
        $this->call(CurriculumSeeder::class);
        $this->call(AttendanceSeeder::class);
    }
}
