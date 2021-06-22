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
        $user1 = User::factory([
            'username' => 'csefaculty1@rit.com',
        ])->create();

        $user2 = User::factory([
            'username' => 'csefaculty2@rit.com',
        ])->create();

        $hod_cse_user = User::factory([
            'username' => 'hodcse@rit.com',
        ])->create();

        $hod_ece_user = User::factory([
            'username' => 'hodece@rit.com',
        ])->create();

        $principal_user = User::factory([
            'username' => 'principal@rit.com',
        ])->create();

        $faculty_1 = Faculty::factory([
            'user_id' => $user1->id,
            'id' => 'faculty_1',
            'department_code' => 'CSE',
        ])->create();

        $faculty_2 = Faculty::factory([
            'user_id' => $user2->id,
            'id' => 'faculty_2',
            'department_code' => 'CSE',
        ])->create();

        $hod_cse = Faculty::factory([
            'user_id' => $hod_cse_user->id,
            'id' => 'hod_cse',
            'department_code' => 'CSE',
        ])->create();

        $hod_ece = Faculty::factory([
            'user_id' => $hod_ece_user->id,
            'id' => 'hod_ece',
            'department_code' => 'ECE'
        ])->create();

        $principal = Faculty::factory([
            'user_id' => $principal_user->id,
            'id' => 'principal',
            'department_code' => "ECE"
        ])->create();

        $user1->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::FACULTY);
        $user2->assignRole(Roles::STAFF_ADVISOR);
        $hod_cse_user->assignRole(Roles::HOD);
        $hod_cse_user->assignRole(Roles::FACULTY);
        $hod_ece_user->assignRole(Roles::HOD);
        $hod_ece_user->assignRole(Roles::FACULTY);
        $principal_user->assignRole(Roles::FACULTY);
        $principal_user->assignRole(Roles::PRINCIPAL);


        $s1_btech_cse = Classroom::where([
            "department_code" => "CSE",
            "semester" => 1,
            "degree_type" => Degrees::BTECH])->first();
        $faculty_2->advisorClassroom()->associate($s1_btech_cse)->save();

        $user1->faculty()->associate($faculty_1)->save();
        $user2->faculty()->associate($faculty_2)->save();
        $hod_cse_user->faculty()->associate($hod_cse)->save();
        $hod_ece_user->faculty()->associate($hod_ece)->save();
        $principal_user->faculty()->associate($principal)->save();
    }
}
