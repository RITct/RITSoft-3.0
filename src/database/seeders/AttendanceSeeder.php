<?php

namespace Database\Seeders;

use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $course = Course::all()->first();
        $student = Student::all()->first();
        $attendance = Attendance::create([
            'date' => '2021-05-31',
            'hour' => 3,
            'course_id' => $course->id
        ]);

        Absentee::create([
            'attendance_id' => $attendance->id,
            'student_admission_id' => $student->admission_id
        ]);

        Attendance::create([
            'date' => '2021-05-30',
            'hour' => 2,
            'course_id' => $course->id
        ]);
    }
}
