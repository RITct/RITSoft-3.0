<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role.create',
            'role.read',
            'role.update',
            'role.delete',
            'role.*',
            'user.create',
            'user.read',
            'user.update',
            'user.delete',
            'user.*',
            'attendance.retrieve',
            'attendance.list',
            'attendance.create',
            'attendance.update',
            'attendance.delete',
            'attendance.*',
            'faculty.retrieve',
            'faculty.list',
            'faculty.create',
            'faculty.update',
            'faculty.delete',
            'faculty.*',
            'feedback.list',
            'feedback.retrieve',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
