<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Student;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $students = Student::with("classroom")->whereHas("classroom", function ($q) {
            $q->where(["semester" => 1, "department_code" => "CSE"]);
        })->get();

        $courses = Course::where("semester", 1)->whereHas("classroom", function ($q) {
            $q->where("department_code", "CSE");
        })->get();

        foreach ($students as $student) {
            Curriculum::create([
                'student_admission_id' => $student->admission_id,
                'course_id' => $courses[0]->id
            ]);

            Curriculum::create([
                'student_admission_id' => $student->admission_id,
                'course_id' => $courses[1]->id
            ]);
        }
    }
}
