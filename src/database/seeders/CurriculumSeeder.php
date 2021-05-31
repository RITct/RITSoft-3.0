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
        $student = Student::all()->first();
        $course = Course::all()->first();

        Curriculum::create([
            'student_admission_id' => $student->admission_id,
            'course_id' => $course->id
        ]);
    }
}
