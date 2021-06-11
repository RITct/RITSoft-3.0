<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Models\Absentee;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{
    public function __construct(){
        $this->middleware("permission:attendance.retrieve", ["only" => "show"]);
        $this->middleware("permission:attendance.list", ["only" => "index"]);
        $this->middleware("permission:attendance.create", ["only" => ["create", "store"]]);
        $this->middleware("permission:attendance.update", ["only" => ["edit", "update"]]);
        $this->middleware("permission:attendance.delete", ["only" => "destroy"]);
    }


    private function serialize_course($data, $faculty=null, $is_admin=null): array {
        $new_data = [];
        foreach ($data as $period) {
            if(array_key_exists($period->course_id, $new_data))
                array_push($new_data[$period->course_id]["attendances"], $period);
            else
                $new_data[$period->course_id] = [
                    "id" => $period->course_id,
                    "faculty" => $period->course->faculty,
                    "subject" => $period->course->subject,
                    "semester" => $period->course->semester,
                    "attendances" => [$period],
                    "editable" => $faculty && $faculty->id == $period->course->faculty_id || $is_admin
                ];
        }
        return $new_data;
    }

    private function parse_attendance_input($inp){
        // This should be an excel/workbook parser
        // For now parse csv

        $data = explode(",", trim($inp));
        return array_filter($data, function ($val){
            return $val != "";
        });
    }

    private function serialize_attendance_from_student($attendance, $student_admission_id, $faculty, $is_admin){
        foreach ($attendance as $period){
            $absent = false;
            foreach ($period->absentees as $absentee) {
                if ($student_admission_id == $absentee->student_admission_id)
                    $absent = true;
                $period->medical_leave = $absentee->medical_leave;
                $period->duty_leave = $absentee->duty_leave;
            }
            $period->absent = $absent;
            if($faculty && $period->course->faculty_id == $faculty->id || $is_admin)
                // Only allow that particular faculty or admin to edit
                $period->editable = true;
            else
                $period->editable = false;
        }
    }

    public function index(AttendanceRequest $request){
        // Principal, Admin can view all
        // HOD can view their dept
        // Staff advisor, Faculty can view their class
        $request->validated();

        $auth_user = Auth::user();
        $faculty = $auth_user->faculty;

        if($auth_user->is_admin())
            // OR Principal
            $query = Attendance::get_base_query($request->get("from"), $request->get("to"));
        else if($faculty && $faculty->is_hod())
            $query = Attendance::get_attendance_of_department(
                $faculty->department->code, $request->get("from"), $request->get("to"));
        else if($faculty)
            $query = Attendance::get_attendance_of_faculty(
                $faculty->id,
                $request->get("from"), $request->get("to"));
        // TODO Staff Advisor
        else
            abort(403);

        // TODO Additional Query Filters like semester, subject, etc

        return view("attendance.index", [
            "attendance" => $this->serialize_course($query->get(), $faculty, $auth_user->is_admin())
        ]);
    }

    public function create(){
        // Only Faculty
        $auth_user = Auth::user();
        $courses = Course::get_base_query();
        if($auth_user->is_admin())
            $courses = $courses->get();
        else {
            $faculty = $auth_user->faculty;
            $courses = $courses->where("faculty_id", $faculty->id)->get();
        }
        return view("attendance.create", ["courses" => $courses]);
    }

    public function store(AttendanceRequest $request){
        // Only Faculty
        $request->validated();
        $auth_user = Auth::user();

        $course = Course::with("curriculums.student")->find($request->input("course_id"));

        if(!$course)
            abort(400, "Course not found");

        /*
        TODO
        $conflicted_attendance = Attendance::where(
            ["date" => $request->input("date"), "hour" => $request->input("hour")])->first();

        if($conflicted_attendance)
            abort(400, "There seems to be another entry with the same date and hour");
       */

        $valid_student_ids = [];
        foreach ($course->curriculums as $curriculum)
            array_push($valid_student_ids, $curriculum->student->admission_id);

        if($auth_user->is_admin() || $course->faculty == $auth_user->faculty){
            $absentee_ids = $this->parse_attendance_input($request->input("absentee_admission_nums"));

            $attendance = new Attendance([
                "date" => $request->input("date"),
                "hour" => $request->input("hour"),
            ]);
            $attendance->course()->associate($course);
            $attendance->save();

            $absentees = [];
            foreach ($absentee_ids as $absentee_id){
                if(!in_array($absentee_id, $valid_student_ids))
                    abort(400,
                        sprintf("Student with admission no %s is not enrolled in your course", $absentee_id));

                $absentee = new Absentee();
                $absentee->student()->associate(
                    $course->curriculums->filter(function ($curriculum) use ($absentee_id){
                        return $curriculum->student->admission_id == $absentee_id;
                    })->first()->student
                );
                $absentee->attendance()->associate($attendance);
                array_push($absentees, $absentee);
            }

            $attendance->absentees()->saveMany($absentees);
            return redirect("/attendance");
        }
        abort(403);
    }

    public function show(AttendanceRequest $request, $student_admission_id){
        // Here student admission id makes more sense than attendance id
        // Same as index
        // Student can view their only
        $request->validated();

        $auth_user = Auth::user();
        $student = $auth_user->student;
        $faculty = $auth_user->faculty;

        if($student != null && $student_admission_id != $student->admission_id)
            // Student trying to access other student
            abort(403);

        if(!$student)
            $student = Student::where('admission_id', $student_admission_id)->first();

        if(!$student)
            // Student doesn't exist
            abort(404, "Student doesn't exist");

        $raw_from_date = $request->input("from");
        $raw_to_date = $request->input("to");

        if($raw_from_date)
            $from_date = date_create($raw_from_date);
        if($raw_to_date)
            $to_date = date_create($raw_to_date);

        $attendance = Attendance::get_attendance_of_student(
            $student_admission_id,
            $from_date ?? null,
            $to_date ?? null
        );

        // HOD of student's dept
        $is_hod = $faculty && $faculty->is_hod() && $student->department_id == $faculty->department_id;

        if($auth_user->student || $faculty && $is_hod || $auth_user->is_admin())
            // TODO: Add principal, Dean etc
            $attendance = $attendance->get();
        elseif ($faculty && !$is_hod)
            // Filter by class
            $attendance = $attendance->whereHas('course.faculty', function ($q) use ($faculty) {
                $q->where('id', $faculty->id);
            })->get();

        $this->serialize_attendance_from_student(
            $attendance, $student_admission_id, $faculty, $auth_user->is_admin());

        return view("attendance.retrieve", [
            "attendance" => $attendance,
            "student_admission_id" => $student_admission_id
        ]);
    }

    public function edit(){
        // Faculty, Staff Advisor/HOD?(For duty leave)
    }

    public function update(){
        // Faculty, Staff Advisor/HOD?(For duty leave)
    }

    public function destroy(){
        // Faculty
    }
}
