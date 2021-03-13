<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\Roles;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => 'csestudent@rit.com',
            'password' => bcrypt('123456')
        ]);

        $user->assignRole(Roles::Student);
    }
}
