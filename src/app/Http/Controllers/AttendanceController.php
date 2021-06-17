<?php

namespace App\Http\Controllers;

use App\Common\CommonAttendance;
use App\Enums\CourseTypes;
use App\Http\Requests\AttendanceRequest;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:attendance.retrieve", ["only" => "show"]);
        $this->middleware("permission:attendance.list", ["only" => "index"]);
        $this->middleware("permission:attendance.create", ["only" => ["create", "store"]]);
        $this->middleware("permission:attendance.update", ["only" => ["edit", "update"]]);
        $this->middleware("permission:attendance.delete", ["only" => "destroy"]);

        $this->middleware("attendance_same_faculty", ["only" => ["edit", "update", "destroy"]]);
        $this->middleware("attendance_same_student", ["only" => "show"]);
    }

    public function index(AttendanceRequest $request)
    {
        // Principal, Admin can view all
        // HOD can view their dept
        // Staff advisor, Faculty can view their class
        $request->validated();

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;

        if ($auth_user->isAdmin()) {
            // OR Principal
            $query = Attendance::getBaseQuery($request->get("from"), $request->get("to"));
        } elseif ($faculty && $faculty->isHOD()) {
            $query = Attendance::getAttendanceOfDepartment(
                $faculty->department->code,
                $request->get("from"),
                $request->get("to")
            );
        } elseif ($faculty) {
            $query = Attendance::getAttendanceOfFaculty(
                $faculty->id,
                $request->get("from"),
                $request->get("to")
            );
        } else {
            abort(403);
        }
        // TODO Additional Query Filters like semester, subject, etc

        return view("attendance.index", [
            "attendance" => CommonAttendance::serializeCourse($query->get(), $faculty, $auth_user->isAdmin())
        ]);
    }

    public function create()
    {
        // Only Faculty
        $auth_user = Auth::user();
        $courses = Course::getBaseQuery();
        if ($auth_user->isAdmin()) {
            $courses = $courses->get();
        } else {
            $faculty = $auth_user->faculty;
            $courses = $courses->where("faculty_id", $faculty->id)->get();
        }
        return view("attendance.create", ["courses" => $courses]);
    }

    public function store(AttendanceRequest $request)
    {
        // Only Faculty
        $request->validated();
        $auth_user = Auth::user();

        $course = Course::with("curriculums.student")->find($request->input("course_id"));

        if (!$course) {
            abort(400, "Course not found");
        }

        $conflicted_attendance = Attendance::where([
            "date" => $request->input("date"),
            "hour" => $request->input("hour"),
        ])->whereHas("course", function ($q) use ($course) {
            $q->where([
                "classroom_id" => $course->classroom_id,
                "type" => CourseTypes::REGULAR
            ]);
        })->first();

        if ($conflicted_attendance) {
            abort(400, "There seems to be another entry with the same date and hour");
        }

        $valid_student_ids = [];
        foreach ($course->curriculums as $curriculum) {
            array_push($valid_student_ids, $curriculum->student->admission_id);
        }
        if ($auth_user->isAdmin() || $course->faculty_id == $auth_user->faculty_id) {
            $absentee_ids = CommonAttendance::parseAttendanceInput($request->input("absentee_admission_nums"));

            $attendance = new Attendance([
                "date" => $request->input("date"),
                "hour" => $request->input("hour"),
            ]);
            $attendance->course()->associate($course);
            $attendance->save();
            $attendance->refresh();

            $absentees = [];
            foreach ($absentee_ids as $absentee_id) {
                if (!in_array($absentee_id, $valid_student_ids)) {
                    abort(
                        400,
                        sprintf(
                            "Student with admission no %s is not enrolled in your course",
                            $absentee_id
                        )
                    );
                }

                // Only arrays support bulk insert
                $absentee = (new Absentee())->toArray();
                $absentee["student_admission_id"] = $course->curriculums->filter(
                    function ($curriculum) use ($absentee_id) {
                        return $curriculum->student->admission_id == $absentee_id;
                    }
                )->first()->student->admission_id;

                $absentee["attendance_id"] = $attendance->id;

                array_push($absentees, $absentee);
            }
            Absentee::insert($absentees);

            return redirect("/attendance");
        }
        abort(403);
    }

    public function show(AttendanceRequest $request, $student_admission_id)
    {
        // Here student admission id makes more sense than attendance id
        $request->validated();

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;

        if (!$auth_user->student) {
            $student = Student::findOrFail($student_admission_id);
        }

        $attendance = Attendance::getAttendanceOfStudent(
            $student_admission_id,
            $request->input("from"),
            $request->input("to")
        );

        // HOD of student's dept
        $is_hod = $faculty && $faculty->isHOD() && $student->department_id == $faculty->department_id;

        if ($auth_user->student || $faculty && $is_hod || $auth_user->isAdmin()) {
            // TODO: Add principal, Dean etc
            $attendance = $attendance->get();
        } elseif ($faculty && !$is_hod) {
            // Filter by course
            $attendance = $attendance->whereHas('course.faculty', function ($q) use ($faculty) {
                $q->where('id', $faculty->id);
            })->get();
        }

        CommonAttendance::serializeAttendanceFromStudent(
            $attendance,
            $student_admission_id,
            $faculty,
            $auth_user->isAdmin()
        );

        return view("attendance.retrieve", [
            "attendance" => $attendance,
            "student_admission_id" => $student_admission_id
        ]);
    }

    public function edit($attendance_id)
    {
        // Faculty, Staff Advisor/HOD?(For duty leave)
        $attendance = Attendance::getBaseQuery()->findOrFail($attendance_id);
        $students = array_map(
            function ($curriculum) {
                return $curriculum["student"];
            },
            $attendance->course->curriculums->toArray()
        );

        // Sort alphabetically
        usort($students, function ($student1, $student2) {
            return strtolower($student1["name"]) <=> strtolower($student2["name"]);
        });
        return view("attendance.edit", [
            "attendance" => $attendance,
            "students" => $students,
        ]);
    }

    public function update(AttendanceRequest $request, $attendance_id)
    {
        // Faculty
        // Staff Advisor/HOD?(For duty leave) -> NOT IMPLEMENTED
        $attendance = Attendance::getBaseQuery()->findOrFail($attendance_id);
        // To aid in removing absentees
        $absentee_exist_map = [];
        foreach ($request->json("absentees", array()) as $admission_id => $leave_type) {
            $absentee = $attendance->absentees->firstWhere("student_admission_id", $admission_id);
            if (!$absentee) {
                // Create new absentee
                $student_curriculum = $attendance->course->curriculums->firstWhere(
                    "student_admission_id",
                    $admission_id
                );
                if (!$student_curriculum) {
                    abort(
                        400,
                        sprintf(
                            "Student with admission id %s doesn't exist, or isn't enrolled in your class",
                            $admission_id
                        )
                    );
                }
                $absentee = new Absentee();
                $absentee->attendance()->associate($attendance);
                $absentee->student()->associate($student_curriculum->student);
            } else {
                $absentee_exist_map[$absentee->id] = true;
            }
            $absentee->leave_excuse = $leave_type;

            // TODO Bulk update is possible?
            $absentee->save();
        }

        $removed_absentees = array_filter(
            $attendance->absentees->toArray(),
            function ($absentee) use ($absentee_exist_map) {
                return !array_key_exists($absentee["id"], $absentee_exist_map);
            }
        );
        Absentee::destroy(
            array_map(
                function ($absentee) {
                    return $absentee["id"];
                },
                $removed_absentees
            )
        );

        return response("OK");
    }

    public function destroy($attendance_id)
    {
        // Faculty
        Attendance::destroy(array($attendance_id));
        return response("OK");
    }
}
