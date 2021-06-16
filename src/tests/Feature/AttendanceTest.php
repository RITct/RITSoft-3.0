<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Curriculum;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\User;
use Database\Factories\StudentFactory;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex() : void
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
    public function testStudentAttendance() : void
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
                if($student_user != $anotherStudent) {
                    $this->actingAs($anotherStudent)
                        ->get($url)
                        ->assertStatus(403);
                }
            }
        }
    }

    public function testAttendanceCreate() : void
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
    }

    private function alterAttendance($mainMethod, $edit=false, $data=array()){
        $all_attendance = Attendance::with("course.faculty")->get();
        foreach ($all_attendance as $attendance){
            $urls = [sprintf("/attendance/%d", $attendance->id) => $mainMethod];
            if($edit) {
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

                if(!$edit)
                    $this->assertEquals(null, Attendance::find($attendance->id));

            }
        }
    }

    public function testAttendanceUpdate() : void
    {
        $absentee = Absentee::with("attendance.course.faculty")->first();
        $faculty = $absentee->attendance->course->faculty;

        $request_datas = [
            [$absentee->student_admission_id => ""],
            [$absentee->student_admission_id => "duty_leave"],
            [$absentee->student_admission_id => "medical_leave"]
        ];

        foreach ($request_datas as $request_data) {
            $this->actingAs($faculty->user)
                ->json(
                    "PATCH",
                    sprintf("/attendance/%d", $absentee->attendance->id),
                    ["absentees" => $request_data]
                )->assertStatus(200);

            $absentee = Absentee::find($absentee->id);
            $this->assertEquals(array_values($request_data)[0] == "medical_leave", $absentee->medical_leave);
            $this->assertEquals(array_values($request_data)[0] == "duty_leave", $absentee->duty_leave);
        }

        // Verify object-level permissions & remove absentees
        $this->alterAttendance("patch", edit: true);
    }

    public function testAttendanceDelete() : void
    {
        $this->alterAttendance("delete");
    }
}
