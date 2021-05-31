<?php

namespace Database\Seeders;

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
        $subject = Subject::where('code', 'CSE201')->first();

        $faculty = Faculty::where('department_code', 'CSE')->first();

        Course::create([
           'subject_code' => $subject->code,
           'faculty_id' => $faculty->faculty_id,
            'semester' => 1
        ]);
    }
}
