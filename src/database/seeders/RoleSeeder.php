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
        $roleAdmin = Role::create(['name' => Roles::Admin]);
        $permissions = Permission::pluck('id','id')->all();
        $roleAdmin->syncPermissions($permissions);

        $attendance_permissions = [
            "view" => ["attendance.retrieve", "attendance.list"],
            "alter" => ["attendance.create", "attendance.update"],
            "delete" => ["attendance.delete"]
        ];

        $roleHOD = Role::create(['name' => Roles::HOD]);
        $roleFaculty = Role::create(['name' => Roles::Faculty]);
        $roleHOD->syncPermissions($attendance_permissions["view"]);
        $roleFaculty->syncPermissions(array_merge($attendance_permissions["view"], $attendance_permissions["alter"]));

        $roleStudent = Role::create(['name' => Roles::Student]);
        $roleStudent->syncPermissions("attendance.retrieve");
    }
}
