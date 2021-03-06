<?php

namespace App\Http\Controllers;

use App\Exceptions\IntendedException;
use App\Http\Requests\AttendanceRequest;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    protected AttendanceService $service;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->middleware("permission:attendance.retrieve", ["only" => "show"]);
        $this->middleware("permission:attendance.list", ["only" => "index"]);
        $this->middleware("permission:attendance.create", ["only" => ["create", "store"]]);
        $this->middleware("permission:attendance.update", ["only" => ["edit", "update"]]);
        $this->middleware("permission:attendance.delete", ["only" => "destroy"]);

        //$this->middleware("attendance_same_faculty", ["only" => ["edit", "update", "destroy"]]);

        $this->service = $attendanceService;
    }

    public function index(AttendanceRequest $request)
    {
        // Principal, Admin can view all
        // HOD can view their dept
        // Staff advisor, Faculty can view their class
        $auth_user = Auth::user();

        $attendance = $this->service->getAllAttendance(
            $request->input("from"),
            $request->input("to"),
            $auth_user
        );
        // TODO Additional Query Filters like semester, subject, etc

        return view("attendance.index", [
            "attendance" => $this->service->serializeCourse($attendance, $auth_user->faculty, $auth_user->isAdmin())
        ]);
    }

    public function create()
    {
        // Only Faculty
        $authUser = Auth::user();
        $courses = Course::getBaseQuery();
        if ($authUser->isAdmin()) {
            $courses = $courses->get();
        } else {
            $courses = $courses->whereHas("faculties", function ($q) use ($authUser) {
                $q->where("faculties.id", $authUser->faculty_id);
            })->get();
        }
        return view("attendance.create", ["courses" => $courses]);
    }

    public function store(AttendanceRequest $request)
    {
        // Only Faculty
        $authUser = Auth::user();
        $date = date_create_from_format("Y-m-d", $request->input("date"));
        $course = Course::with("curriculums.student")->find($request->input("course_id"));

        if (!$course) {
            abort(400, "Course not found");
        }

        if ($this->service->attendanceIsConflicted($date, $request->input("hour"), $course->classroom_id)) {
            abort(400, "Timing conflict");
        }

        if ($this->service->isDateInFuture($date)) {
            abort(400, "Date shouldn't be in the future");
        }

        $validStudentIds = [];
        foreach ($course->curriculums as $curriculum) {
            array_push($validStudentIds, $curriculum->student->admission_id);
        }

        if ($authUser->isAdmin() || $course->hasFaculty($authUser->faculty_id)) {
            $absenteeIds = $this->service->parseAttendanceInput($request->input("absentee_admission_nums"));
            $attendance = Attendance::createAttendance($date, $request->input("hour"), $course);

            $absentees = [];
            foreach ($absenteeIds as $absenteeId) {
                if (!in_array($absenteeId, $validStudentIds)) {
                    $error = sprintf("Student with admission no %s is not enrolled in your course", $absenteeId);
                    abort(400, $error);
                }

                // Only arrays support bulk insert
                array_push($absentees, $this->service->getAbsenteeAsArray($course, $absenteeId, $attendance->id));
            }
            Absentee::insert($absentees);

            return redirect()->route("attendance.index");
        }
        abort(403);
    }

    public function show(AttendanceRequest $request, $studentAdmissionId)
    {
        // Here student admission id makes more sense than attendance id

        $authUser = Auth::user();
        $faculty = $authUser->faculty;

        $student = Student::findOrFail($studentAdmissionId);

        $attendance = $this->service->getAttendanceOfStudent(
            $student,
            $authUser,
            $request->input("from"),
            $request->input("to")
        );

        $this->service->serializeAttendanceFromStudent(
            $attendance,
            $studentAdmissionId,
            $faculty,
            $authUser->isAdmin()
        );

        return view("attendance.retrieve", [
            "attendance" => $attendance,
            "student_admission_id" => $studentAdmissionId
        ]);
    }

    public function edit(AttendanceRequest $request)
    {
        // Faculty, Staff Advisor/HOD?(For duty leave)
        $students = $this->service->getStudentsFromCurriculums($request->attendance->course->curriculums);

        // Sort alphabetically
        usort($students, function ($student1, $student2) {
            return strtolower($student1["name"]) <=> strtolower($student2["name"]);
        });
        return view("attendance.edit", [
            "attendance" => $request->attendance,
            "students" => $students,
        ]);
    }

    public function update(AttendanceRequest $request)
    {
        // Faculty
        // Staff Advisor/HOD?(For duty leave) -> NOT IMPLEMENTED

        // To aid in removing absentees
        $absenteeExistMap = [];

        foreach ($request->json("absentees", array()) as $admissionId => $leaveType) {
            $absentee = $request->attendance->absentees->firstWhere("student_admission_id", $admissionId);
            if (!$absentee) {
                // Create new absentee
                try {
                    $absentee = $this->service->createAbsentee($request->attendance, $admissionId);
                } catch (IntendedException $e) {
                    abort(400, $e->getMessage());
                }
            } else {
                $absenteeExistMap[$absentee->id] = true;
            }
            $absentee->leave_excuse = $leaveType;

            // TODO Bulk update is possible?
            $absentee->save();
        }

        $removed_absentees = array_filter(
            $request->attendance->absentees->toArray(),
            function ($absentee) use ($absenteeExistMap) {
                return !array_key_exists($absentee["id"], $absenteeExistMap);
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

    public function destroy(AttendanceRequest $request)
    {
        // Faculty
        $request->attendance->delete();
        return response("OK");
    }
}
