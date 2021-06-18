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

        $attendance_permissions = [
            "view" => ["attendance.retrieve", "attendance.list"],
            "alter" => ["attendance.create", "attendance.update"],
            "delete" => ["attendance.delete"],
            "all" => "attendance.*"
        ];

        $roleHOD = Role::create(['name' => Roles::HOD]);
        $roleFaculty = Role::create(['name' => Roles::FACULTY]);
        $roleAdvisor = Role::create(["name" => Roles::STAFF_ADVISOR]);
        $roleHOD->syncPermissions($attendance_permissions["view"]);
        $roleFaculty->syncPermissions($attendance_permissions["all"]);
        $roleAdvisor->syncPermissions($attendance_permissions["view"]);

        $roleStudent = Role::create(['name' => Roles::STUDENT]);
        $roleStudent->syncPermissions("attendance.retrieve");
    }
}
