<?php

namespace Database\Seeders;

use App\Enums\CourseTypes;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Faculty;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subject1 = Subject::where('code', 'CSE201')->first();
        $subject2 = Subject::where('code', 'CSE301')->first();
        $s1classroom = Classroom::where(["semester" => 1, "department_code" => "CSE"])->first();

        $course1 = Course::create([
            'subject_code' => $subject1->code,
            'type' => CourseTypes::REGULAR,
            'semester' => 1,
            'classroom_id' => $s1classroom->id
        ]);

        $course2 = Course::create([
            'subject_code' => $subject2->code,
            'type' => CourseTypes::REGULAR,
            'semester' => 1,
            'classroom_id' => $s1classroom->id
        ]);
        $course1->faculties()->attach(Faculty::find("faculty_1"));
        $course1->faculties()->attach(Faculty::find("hod_cse"));
        $course2->faculties()->attach(Faculty::find("faculty_2"));
    }
}
