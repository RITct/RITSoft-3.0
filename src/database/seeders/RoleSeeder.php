<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::create(['name' => Roles::ADMIN]);
        $permissions = Permission::pluck('id', 'id')->all();
        $roleAdmin->syncPermissions($permissions);

        $attendancePermissions = [
            "view" => ["attendance.retrieve", "attendance.list"],
            "alter" => ["attendance.create", "attendance.update"],
            "delete" => ["attendance.delete"],
            "all" => "attendance.*"
        ];
        $facultyPermissions = [
            "view" => ["faculty.retrieve", "faculty.list"],
            "alter" => ["faculty.create", "faculty.update"],
            "delete" => ["faculty.delete"],
            "all" => "faculty.*"
        ];
        $feedbackPermissions = [
            "view" => ["feedback.retrieve", "feedback.list"],
        ];

        $roleHOD = Role::create(['name' => Roles::HOD]);
        $roleFaculty = Role::create(['name' => Roles::FACULTY]);
        $roleAdvisor = Role::create(["name" => Roles::STAFF_ADVISOR]);
        $rolePrincipal = Role::create(["name" => Roles::PRINCIPAL]);

        $roleFaculty->syncPermissions(
            $attendancePermissions["all"],
            ["faculty.retrieve", "faculty.update"],
            $feedbackPermissions["view"]
        );
        $roleHOD->syncPermissions(
            ["faculty.create", "faculty.delete"],
            $facultyPermissions["view"]
        );
        $rolePrincipal->syncPermissions($facultyPermissions["view"]);

        $roleStudent = Role::create(['name' => Roles::STUDENT]);
        $roleStudent->syncPermissions("attendance.retrieve");

        $roleOffice = Role::create(['name' => Roles::OFFICE]);
    }
}
