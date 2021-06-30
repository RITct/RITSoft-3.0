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
    public function testIndex(): void
    {
        // Attendance requires login
        $this->assertLoginRequired(route("listAttendance"));

        $this->assertUsersOnEndpoint(
            "/attendance",
            "get",
            array(
                Roles::ADMIN => 200,
                Roles::HOD => 200,
                Roles::FACULTY => 200,
                Roles::PRINCIPAL => 200,
                Roles::STUDENT => 403,
            )
        );
    }
    public function testStudentAttendance(): void
    {
        foreach ($this->users[Roles::STUDENT] as $student_user) {
            $url = sprintf("/attendance/%s", $student_user->student_admission_id);
            $this->assertLoginRequired($url);
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
        $this->assertLoginRequired(route("createAttendance"));
        $this->assertLoginRequired(route("storeAttendance"), "post");
        $this->assertUsersOnEndpoint(
            route("createAttendance"),
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
            ->post(route("storeAttendance"), ["date" => "abcd", "hour" => "mosf", "course_id" => "jwse"])
            ->assertRedirect(route("createAttendance"));

        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post(route("storeAttendance"), ["hour" => 1, "course_id" => 1])
            ->assertRedirect(route("createAttendance"));

        // Invalid course id
        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post(route("storeAttendance"), ["date" => "29-11-2021", "hour" => 1, "course_id" => 0])
            ->assertStatus(400);

        // Test conflicting attendance
        $attendance = Attendance::factory(["course_id" => Course::all()->first()->id])->create();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->post(route("storeAttendance"), [
                "date" => $attendance->date,
                "hour" => $attendance->hour,
                "course_id" => $attendance->course_id
            ])->assertStatus(400);

        // Future date shouldn't work
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->post(route("storeAttendance"), [
                "course_id" => Course::all()->random()->id,
                "date" => date("Y-m-d", strtotime("tomorrow")),
                "hour" => 3,
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
                ->post(route("storeAttendance"), $array_attendance)
                ->assertRedirect(route("listAttendance"));

            // Check in DB
            $this->assertNotNull(Attendance::where($array_attendance)->first());
            $hour++;
        }
    }

    private function alterAttendance($method, $edit = false, $data = array()): void
    {
        $allAttendance = Attendance::with("course.faculty")->get();
        foreach ($allAttendance as $attendance) {
            $url = route("updateAttendance", $attendance->id);
            $this->assertLoginRequired($url, $method);

            if ($edit) {
                $urls[sprintf("%s/edit", $url)] = "get";
            }
            $validUsers = array(
                $this->pickRandomUser(Roles::ADMIN),
                User::find($attendance->course->faculty->user_id)
            );

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
                array($this->actingAs($validUsers[array_rand($validUsers)]), $method),
                $url,
                $data
            )->assertStatus(200);

            if (!$edit) {
                $this->assertEquals(null, Attendance::find($attendance->id));
            }
        }
    }

    public function testAttendanceUpdate(): void
    {
        $absentee = Absentee::with("attendance.course.faculty")->first();
        $faculty = $absentee->attendance->course->faculty;

        $requestDatas = [
            [$absentee->student_admission_id => LeaveType::NO_EXCUSE],
            [$absentee->student_admission_id => LeaveType::DUTY_LEAVE],
            [$absentee->student_admission_id => LeaveType::MEDICAL_LEAVE]
        ];

        foreach ($requestDatas as $request_data) {
            $url = route("updateAttendance", $absentee->attendance->id);
            $this->actingAs($faculty->user)
                ->json(
                    "PATCH",
                    $url,
                    ["absentees" => $request_data]
                )->assertStatus(200);

            $absentee = Absentee::find($absentee->id);
            $this->assertEquals(array_values($request_data)[0], $absentee->leave_excuse);
        }

        // Try to add a student who's not enrolled in this course
        $studentUser = User::factory()->create();
        $student = Student::factory(["user_id" => $studentUser->id])->create();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->json(
                "PATCH",
                sprintf("/attendance/%d", $absentee->attendance->id),
                ["absentees" => [$student->admission_id => LeaveType::NO_EXCUSE]]
            )->assertStatus(400);

        // Create a new absentee
        $randomCourse = Course::with("curriculums.student")->get()->random();
        $randomStudentId = $randomCourse->curriculums->random()->student_admission_id;
        $attendance = Attendance::factory(["course_id" => $randomCourse->id])->create();
        $leaveExcuse = LeaveType::getRandomValue();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->json(
                "PATCH",
                sprintf("/attendance/%d", $attendance->id),
                ["absentees" => [$randomStudentId => $leaveExcuse]]
            )->assertStatus(200);

        $absenteeInDB = Attendance::find($attendance->id)->absentees->first();
        $this->assertEquals($absenteeInDB->leave_excuse, $leaveExcuse);
        // Verify object-level permissions & remove absentees
        $this->alterAttendance("patch", edit: true);
    }

    public function testAttendanceDelete(): void
    {
        $this->alterAttendance("delete");
    }
}
