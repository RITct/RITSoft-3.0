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
        $subject1 = Subject::where('code', 'CSE201')->first();
        $subject2 = Subject::where('code', 'CSE301')->first();
        $faculty = Faculty::where('department_code', 'CSE')->get();

        Course::create([
            'subject_code' => $subject1->code,
            'faculty_id' => $faculty[0]->id,
            'semester' => 1
        ]);

        Course::create([
            'subject_code' => $subject2->code,
            'faculty_id' => $faculty[1]->id,
            'semester' => 1
        ]);
    }
}
