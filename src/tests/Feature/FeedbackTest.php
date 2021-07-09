<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Feedback;
use App\Models\Student;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    public function test_feedback_list()
    {
        $this->assertLoginRequired(route("feedbacks.index"));
        $this->assertUsersOnEndpoint(
            route("feedbacks.index"),
            "get",
            [
                Roles::STUDENT => 403,
                Roles::OFFICE => 403,
                Roles::ADMIN => 200,
                Roles::FACULTY => 200
            ]
        );
    }

    public function testFeedbackRetrieve()
    {
        $feedbacks = Feedback::with("faculty.user")->get();
        foreach ($feedbacks as $feedback) {
            $url = route("feedbacks.show", $feedback->id);
            $this->assertUsersOnEndpoint(
                $url,
                "get",
                [
                    Roles::ADMIN => 200,
                    Roles::PRINCIPAL => 200
                ]
            );

            $otherFaculties = array_filter($this->users[Roles::FACULTY], function ($user) use ($feedback) {
                return $user->faculty_id != $feedback->faculty_id;
            });
            $otherHODs = array_filter($this->users[Roles::HOD], function ($user) use ($feedback) {
                return $user->faculty_id != $feedback->faculty_id;
            });
            $unauthorisedUsers = array_merge($otherFaculties, $otherHODs);
            foreach ($unauthorisedUsers as $unauthorisedUser) {
                $this->actingAs($unauthorisedUser)->get($url)
                    ->assertStatus(403);
            }

            $req = $this->actingAs($feedback->faculty->user)->get($url)
                ->assertStatus(200);
            $req->assertStatus(200);
            $this->actingAs($feedback->faculty->department->getHOD()->user)->get($url)
                ->assertStatus(200);
        }
    }

    public function testCourseRetrieve()
    {

    }

    public function testCreate()
    {
        $courses = Course::with("faculties", "curriculums.student")
            ->where("is_feedback_open", true)
            ->get();
        $this->assertLoginRequired(route("feedbacks.create", $courses->first()->id));
        foreach ($courses as $course) {
            $students = $course->curriculums->map(function ($curriculum) {
                return $curriculum->student;
            });
            $otherStudents = Student::whereHas("curriculums", function ($q) use ($course) {
                $q->where("course_id", "!=", $course->id);
            });
            $formUrl = route("feedbacks.create", $course->id);
            $storeUrl = route("feedbacks.store", $course->id);
            $this->assertUsersOnEndpoint(
                $formUrl,
                "get",
                [
                    Roles::OFFICE => 403,
                    Roles::ADMIN => 403,
                    Roles::FACULTY => 403,
                    Roles::HOD => 403,
                    Roles::PRINCIPAL => 403
                ]
            );
            foreach ($students as $student) {
                $this->actingAs($student->user)->get($formUrl)
                    ->assertStatus(200);
                $data = [];
                foreach ($course->faculties as $faculty) {
                    $data[$faculty->id] = Feedback::$testFeedback;
                }

                $this->actingAs($student->user)->json("post", $storeUrl, ["data" => $data])
                    ->assertStatus(200);

                $targetCurriculum = Curriculum::query()
                    ->where([
                        "student_admission_id" => $student->admission_id,
                        "course_id" => $course->id
                    ])->get();

                $this->assertTrue($targetCurriculum->is_feedback_complete);

                // Can't do feedback more than once
                $this->actingAs($student->user)->json("post", $storeUrl, ["data" => $data])
                    ->assertStatus(409);
            }
            foreach ($otherStudents as $student) {
                $this->actingAs($student->user)->get($formUrl)
                    ->assertStatus(403);
            }
        }
    }
}
