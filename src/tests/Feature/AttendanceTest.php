<?php

namespace Tests\Feature;

use App\Enums\LeaveType;
use App\Enums\Roles;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    public function testIndex(): void
    {
        // Attendance requires login
        $this->assertLoginRequired(route("attendance.index"));

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
        $this->assertLoginRequired(route("attendance.create"));
        $this->assertLoginRequired(route("attendance.store"), "post");
        $this->assertUsersOnEndpoint(
            route("attendance.create"),
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
            ->post(route("attendance.store"), ["date" => "abcd", "hour" => "mosf", "course_id" => "jwse"])
            ->assertRedirect(route("attendance.create"));

        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post(route("attendance.store"), ["hour" => 1, "course_id" => 1])
            ->assertRedirect(route("attendance.create"));

        // Invalid course id
        $this->actingAs($this->pickRandomUser(Roles::FACULTY))
            ->post(route("attendance.store"), ["date" => "29-11-2021", "hour" => 1, "course_id" => 0])
            ->assertStatus(400);

        // Test conflicting attendance
        $attendance = Attendance::factory(["course_id" => Course::all()->first()->id])->create();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->post(route("attendance.store"), [
                "date" => $attendance->date,
                "hour" => $attendance->hour,
                "course_id" => $attendance->course_id
            ])->assertStatus(400);

        // Future date shouldn't work
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->post(route("attendance.store"), [
                "course_id" => Course::all()->random()->id,
                "date" => date("Y-m-d", strtotime("tomorrow")),
                "hour" => 3,
            ])->assertStatus(400);

        // Hour is changed, to prevent conflicting attendance records because I dont trust laravel
        $hour = 1;
        foreach (Course::with("faculties.user")->get() as $course) {
            $attendance = Attendance::factory(["course_id" => $course->id, "hour" => $hour])->make();
            $validUsers = $course->faculties->map(
                function ($faculty) {
                    return $faculty->user;
                }
            );
            $validUsers->push($this->pickRandomUser(Roles::ADMIN));
            // these 3 values together are unique
            $arrayAttendance = [
                "date" => $attendance->date,
                "hour" => $attendance->hour,
                "course_id" => $attendance->course_id
            ];
            // Either correct faculty or Admin(Random)
            $this->actingAs($validUsers->random())
                ->post(route("attendance.store"), $arrayAttendance)
                ->assertRedirect(route("attendance.index"));

            // Check in DB
            $this->assertNotNull(Attendance::where($arrayAttendance)->first());
            $hour++;
        }
    }

    private function alterAttendance($method, $edit = false, $data = array()): void
    {
        $allAttendance = Attendance::with("course.faculties")->get();
        foreach ($allAttendance as $attendance) {
            $url = route("attendance.update", $attendance->id);
            $this->assertLoginRequired($url, $method);

            if ($edit) {
                $urls[sprintf("%s/edit", $url)] = "get";
            }
            $validUsers = $attendance->course->faculties->map(function ($faculty) {
                return $faculty->user;
            });
            $validUsers->push($this->pickRandomUser(Roles::ADMIN));
            $validFacultyIds = $attendance->course->faculties->map(function ($faculty) {
                return $faculty->id;
            });
            // Non admin users other than the faculty that owns the attendance are all denied delete
            $users = User::whereNotIn("faculty_id", $validFacultyIds->toArray())
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
                array($this->actingAs($validUsers->random()), $method),
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
        $absentee = Absentee::with("attendance.course.faculties")->first();
        $faculties = $absentee->attendance->course->faculties;
        foreach ($faculties as $faculty) {
            $requestDatas = [
                [$absentee->student_admission_id => LeaveType::NO_EXCUSE],
                [$absentee->student_admission_id => LeaveType::DUTY_LEAVE],
                [$absentee->student_admission_id => LeaveType::MEDICAL_LEAVE]
            ];

            foreach ($requestDatas as $request_data) {
                $url = route("attendance.update", $absentee->attendance->id);
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
                    $url,
                    ["absentees" => [$student->admission_id => LeaveType::NO_EXCUSE]]
                )->assertStatus(400);
        }

        // Create a new absentee
        $randomCourse = Course::with("curriculums.student")->get()->random();
        $randomStudentId = $randomCourse->curriculums->random()->student_admission_id;
        $attendance = Attendance::factory(["course_id" => $randomCourse->id])->create();
        $leaveExcuse = LeaveType::getRandomValue();
        $this->actingAs($this->pickRandomUser(Roles::ADMIN))
            ->json(
                "PATCH",
                route("attendance.update", $attendance->id),
                ["absentees" => [$randomStudentId => $leaveExcuse]]
            )->assertStatus(200);

        $absenteeInDB = Attendance::find($attendance->id)->absentees->first();
        $this->assertEquals($absenteeInDB->leave_excuse, $leaveExcuse);

        // Verify object-level permissions
        $this->alterAttendance("patch", edit: true);
    }

    public function testAttendanceDelete(): void
    {
        $this->alterAttendance("delete");
    }
}
