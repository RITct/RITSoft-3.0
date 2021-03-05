<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class FacultyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'CSE Faculty 1',
            'email' => 'csefaculty1@rit.com',
            'password' => bcrypt('123456')
        ]);

        $user->assignRole(Roles::Faculty);
    }
}
