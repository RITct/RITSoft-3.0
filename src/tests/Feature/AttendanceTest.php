<?php

namespace Tests\Feature;

use App\Enums\LeaveType;
use App\Enums\Roles;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        // Attendance requires login
        $this->get('/attendance')->assertRedirect("/auth/login");

        $this->assertUsersOnEndpoint(
            "/attendance",
            "get",
            array(
                Roles::ADMIN => 200,
                Roles::HOD => 200,
                Roles::FACULTY => 200,
                Roles::STUDENT => 403,
            )
        );
    }
    public function testStudentAttendance(): void
    {
        foreach ($this->users[Roles::STUDENT] as $student_user) {
            $url = sprintf("/attendance/%s", $student_user->student_admission_id);
            $this->assertUsersOnEndpoint(
                $url,
                "get",
                array(
                    Roles::ADMIN => 200,
                    Roles::HOD => 200,
                    Roles::FACULTY => 200,
                )
            );
            // Check object permissions
            $this->actingAs($student_user)
                ->get($url)
                ->assertStatus(200);

            foreach ($this->users[Roles::STUDENT] as $anotherStudent) {
                if ($student_user != $anotherStudent) {
                    $this->actingAs($anotherStudent)
                        ->get($url)
                        ->assertStatus(403);
                }
            }
        }
    }

    public function testAttendanceCreate(): void
    {
        $this->assertUsersOnEndpoint(
            "/attendance/create",
            "get",
            array(
                Roles::ADMIN => 200,
                Roles::FACULTY => 200,
                // HOD, Staff Advisor, Principal is a faculty so, this endpoint would be accessible
                Roles::STUDENT => 403
            )
        );

        // Validation Errors
        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post("/attendance", ["date" => "abcd", "hour" => "mosf", "course_id" => "jwse"])
            ->assertRedirect("/attendance/create");

        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post("/attendance", ["hour" => 1, "course_id" => 1])
            ->assertRedirect("/attendance/create");

        // Invalid course id
        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post("/attendance", ["date" => "29-11-2021", "hour" => 1, "course_id" => 0])
            ->assertStatus(400);

        // Test conflicting attendance
        $attendance = Attendance::factory(["course_id" => Course::all()->first()->id])->create();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->post("/attendance", [
                "date" => $attendance->date,
                "hour" => $attendance->hour,
                "course_id" => $attendance->course_id
            ])->assertStatus(400);

        // Hour is changed, to prevent conflicting attendance records because I dont trust laravel
        $hour = 1;
        foreach (Course::with("faculty.user")->get() as $course) {
            $attendance = Attendance::factory(["course_id" => $course->id, "hour" => $hour])->make();

            $valid_users = [$course->faculty->user, $this->pickRandomUser(Roles::ADMIN)];
            // these 3 values together are unique
            $array_attendance = [
                "date" => $attendance->date,
                "hour" => $attendance->hour,
                "course_id" => $attendance->course_id
            ];
            // Either correct faculty or Admin(Random)
            $this->actingAs($valid_users[array_rand($valid_users)])
                ->post("/attendance", $array_attendance)
                ->assertRedirect("/attendance");

            // Check in DB
            $this->assertNotNull(Attendance::where($array_attendance)->first());
            $hour++;
        }
    }

    private function alterAttendance($mainMethod, $edit = false, $data = array()): void
    {
        $all_attendance = Attendance::with("course.faculty")->get();
        foreach ($all_attendance as $attendance) {
            $urls = [sprintf("/attendance/%d", $attendance->id) => $mainMethod];
            if ($edit) {
                $urls[sprintf("/attendance/%d/edit", $attendance->id)] = "get";
            }
            $valid_users = array(
                $this->pickRandomUser(Roles::ADMIN),
                User::find($attendance->course->faculty->user_id)
            );
            foreach ($urls as $url => $method) {
                // Non admin users other than the faculty that owns the attendance are all denied delete
                $users = User::where("faculty_id", "!=", $attendance->course->faculty_id)
                    ->whereHas("roles", function ($q) {
                        return $q->where("name", "!=", Roles::ADMIN);
                    })->get();
                foreach ($users as $user) {
                    call_user_func(
                        array($this->actingAs($user), $method),
                        $url,
                        $data
                    )->assertStatus(403);
                }

                // Choose either admin/valid faculty randomly and perform operation
                call_user_func(
                    array($this->actingAs($valid_users[array_rand($valid_users)]), $method),
                    $url,
                    $data
                )->assertStatus(200);

                if (!$edit) {
                    $this->assertEquals(null, Attendance::find($attendance->id));
                }
            }
        }
    }

    public function testAttendanceUpdate(): void
    {
        $absentee = Absentee::with("attendance.course.faculty")->first();
        $faculty = $absentee->attendance->course->faculty;

        $request_datas = [
            [$absentee->student_admission_id => LeaveType::NO_EXCUSE],
            [$absentee->student_admission_id => LeaveType::DUTY_LEAVE],
            [$absentee->student_admission_id => LeaveType::MEDICAL_LEAVE]
        ];

        foreach ($request_datas as $request_data) {
            $this->actingAs($faculty->user)
                ->json(
                    "PATCH",
                    sprintf("/attendance/%d", $absentee->attendance->id),
                    ["absentees" => $request_data]
                )->assertStatus(200);

            $absentee = Absentee::find($absentee->id);
            $this->assertEquals(array_values($request_data)[0], $absentee->leave_excuse);
        }

        // Try to add a student who's not enrolled in this course
        $student_user = User::factory()->create();
        $student = Student::factory(["user_id" => $student_user->id])->create();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->json(
                "PATCH",
                sprintf("/attendance/%d", $absentee->attendance->id),
                ["absentees" => [$student->admission_id => LeaveType::NO_EXCUSE]]
            )->assertStatus(400);

        // Create a new absentee
        $random_course = Course::with("curriculums.student")->get()->random();
        $random_student_id = $random_course->curriculums->random()->student_admission_id;
        $attendance = Attendance::factory(["course_id" => $random_course->id])->create();
        $leave_excuse = LeaveType::getRandomValue();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->json(
                "PATCH",
                sprintf("/attendance/%d", $attendance->id),
                ["absentees" => [$random_student_id => $leave_excuse]]
            )->assertStatus(200);

        $absentee_in_db = Attendance::find($attendance->id)->absentees->first();
        $this->assertEquals($absentee_in_db->leave_excuse, $leave_excuse);
        // Verify object-level permissions & remove absentees
        $this->alterAttendance("patch", edit: true);
    }

    public function testAttendanceDelete(): void
    {
        $this->alterAttendance("delete");
    }
}
