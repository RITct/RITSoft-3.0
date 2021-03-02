<?php

namespace Database\Seeders;

use App\Models\User;
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
        $roleAdmin = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('id','id')->all();
        $roleAdmin->syncPermissions($permissions);

        $roleHod = Role::create(['name' => 'HOD']);
        $roleHod->syncPermissions(['subject-edit', 'subject-create', 'subject-delete', 'subject-list']);

        $roleFaculty = Role::create(['name' => 'Faculty']);
        $roleFaculty->syncPermissions(['subject-list']);

    }
}
