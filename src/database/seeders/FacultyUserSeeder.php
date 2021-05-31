<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Department;
use App\Models\Faculty;
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
            'username' => 'csefaculty1@rit.com',
            'password' => '123456'
        ]);

        $cse = Department::where('code', 'CSE')->first();

        Faculty::create([
            'user_id' => $user->id,
            'faculty_id' => 'blah_blah',
            'name' => 'cse_faculty',
            'phone' => '1234567890',
            'address' => 'xyz',
            'department_code' => $cse->code,
        ]);
        $user->assignRole(Roles::Faculty);

        //$profile->user()->save();
    }
}
