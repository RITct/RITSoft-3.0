<?php

namespace Database\Seeders;

use App\Models\Classroom;
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
    private function create_student($i, $classroom_id){
        $user = User::create([
            'username' => sprintf('csestudent%d@rit.com', $i),
            'password' => '123456'
        ]);

        $user->assignRole(Roles::Student);

        $profile = Student::create([
            'user_id' => $user->id,
            'admission_id' => sprintf('19brxxxx%d', $i),
            'roll_no' => $i + 1,
            'name' => sprintf('student%d', $i),
            'phone' => sprintf('123456789%d', $i),
            'address' => 'xyz',
            'classroom_id' => $classroom_id
        ]);

        $profile->user()->save($user);
    }
    public function run()
    {

        $s1classroom = Classroom::where(["semester" => 1, "department_code" => "CSE"])->first();
        for ($i=0; $i < 3; $i++)
            $this->create_student($i, $s1classroom->id);
    }
}
