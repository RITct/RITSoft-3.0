<?php

namespace Database\Seeders;

use App\Enums\Degrees;
use App\Enums\Roles;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FacultyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::create([
            'username' => 'csefaculty1@rit.com',
            'password' => '123456'
        ]);

        $user2 = User::create([
            'username' => 'csefaculty2@rit.com',
            'password' => '123456'
        ]);
        $user3 = User::create([
            'username' => 'hodcse@rit.com',
            'password' => '123456'
        ]);

        $cse = Department::where('code', 'CSE')->first();

        $faculty_1 = Faculty::create([
            'user_id' => $user1->id,
            'id' => 'faculty_1',
            'name' => 'cse_faculty',
            'phone' => '1234567890',
            'address' => 'xyz',
            'department_code' => $cse->code,
        ]);

        $faculty_2 = Faculty::create([
            'user_id' => $user2->id,
            'id' => 'faculty_2',
            'name' => 'cse_faculty_2',
            'phone' => '1234568190',
            'address' => 'xyz',
            'department_code' => $cse->code,
        ]);

        $faculty_3 = Faculty::create([
            'user_id' => $user3->id,
            'id' => 'hod_cse',
            'name' => 'hod_cse',
            'phone' => '1234568110',
            'address' => 'xyz',
            'department_code' => $cse->code,
        ]);

        $user1->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::STAFF_ADVISOR);
        $user3->assignRole(Roles::HOD);
        $user3->assignRole(Roles::FACULTY);

        $s1_btech_cse = Classroom::where([
            "department_code" => "CSE",
            "semester" => 1,
            "degree_type" => Degrees::BTECH]
        )->first();
        $faculty_2->advisor_classroom()->associate($s1_btech_cse)->save();

        $user1->faculty()->associate($faculty_1)->save();
        $user2->faculty()->associate($faculty_2)->save();
        $user3->faculty()->associate($faculty_3)->save();
    }
}
