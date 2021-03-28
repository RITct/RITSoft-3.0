<?php

namespace Database\Seeders;

use App\Models\Student;
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
            'password' => '123456'
        ]);

        $user->assignRole(Roles::Student);

        $profile = Student::create([
           'user_id' => $user->id,
           'admission_id' => '19brxxxxx',
           'current_sem' => '1',
           'name' => 'student',
           'phone' => '1234567890',
           'address' => 'xyz'
        ]);

        $profile->user()->save($user);
    }
}
