<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $user = User::create([
            'name' => 'RITSOFT Admin',
            'email' => 'admin@rit.com',
            'password' => bcrypt('123456')
        ]);

        $user->assignRole(Roles::Admin);

    }
}
