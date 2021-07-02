<?php

namespace Database\Seeders;

use App\Enums\Degrees;
use App\Enums\Roles;
use App\Models\Classroom;
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
        $user1 = User::factory([
            'username' => 'csefaculty1@rit.com',
        ])->create();

        $user2 = User::factory([
            'username' => 'csefaculty2@rit.com',
        ])->create();

        $user3 = User::factory([
            'username' => 'csefaculty3@rit.com',
        ])->create();

        $hodCSEUser = User::factory([
            'username' => 'hodcse@rit.com',
        ])->create();

        $hodECEUser = User::factory([
            'username' => 'hodece@rit.com',
        ])->create();

        $principalUser = User::factory([
            'username' => 'principal@rit.com',
        ])->create();

        $faculty1 = Faculty::factory([
            'user_id' => $user1->id,
            'id' => 'faculty_1',
            'department_code' => 'CSE',
        ])->create();

        $faculty2 = Faculty::factory([
            'user_id' => $user2->id,
            'id' => 'faculty_2',
            'department_code' => 'CSE',
        ])->create();

        $faculty3 = Faculty::factory([
            'user_id' => $user3->id,
            'id' => 'faculty_3',
            'department_code' => 'CSE',
        ])->create();

        $hodCSE = Faculty::factory([
            'user_id' => $hodCSEUser->id,
            'id' => 'hod_cse',
            'department_code' => 'CSE',
        ])->create();

        $hodECE = Faculty::factory([
            'user_id' => $hodECEUser->id,
            'id' => 'hod_ece',
            'department_code' => 'ECE'
        ])->create();

        $principal = Faculty::factory([
            'user_id' => $principalUser->id,
            'id' => 'principal',
            'department_code' => "ECE"
        ])->create();

        $user1->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::STAFF_ADVISOR);
        $user3->assignRole(Roles::FACULTY);
        $hodCSEUser->assignRole(Roles::HOD);
        $hodCSEUser->assignRole(Roles::FACULTY);
        $hodECEUser->assignRole(Roles::HOD);
        $hodECEUser->assignRole(Roles::FACULTY);
        $principalUser->assignRole(Roles::FACULTY);
        $principalUser->assignRole(Roles::PRINCIPAL);


        $s1BtechCSE = Classroom::where([
            "department_code" => "CSE",
            "semester" => 1,
            "degree_type" => Degrees::BTECH])->first();
        $faculty2->advisorClassroom()->associate($s1BtechCSE)->save();

        $user1->faculty()->associate($faculty1)->save();
        $user2->faculty()->associate($faculty2)->save();
        $user3->faculty()->associate($faculty3)->save();
        $hodCSEUser->faculty()->associate($hodCSE)->save();
        $hodECEUser->faculty()->associate($hodECE)->save();
        $principalUser->faculty()->associate($principal)->save();
    }
}
