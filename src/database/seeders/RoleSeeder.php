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

        $attendance_view = ["attendance.retrieve", "attendance.list"];
        $roleHOD = Role::create(['name' => Roles::HOD]);
        $roleFaculty = Role::create(['name' => Roles::Faculty]);
        $roleHOD->syncPermissions($attendance_view);
        $roleFaculty->syncPermissions($attendance_view);

        $roleStudent = Role::create(['name' => Roles::Student]);
        $roleStudent->syncPermissions("attendance.retrieve");
    }
}
