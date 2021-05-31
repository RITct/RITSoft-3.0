<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
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

    public function index(){
        // Principal, Admin can view all
        // HOD can view their dept
        // Staff advisor, Faculty can view their class

    }

    public function create(){
        // Only Faculty
    }

    public function store(){
        // Only Faculty
    }

    public function show(Request $request, $student_admission_id){
        // Here student admission id makes more sense than attendance id
        // Same as index
        // Student can view their only
        $student = Auth::user()->student;

        if($student != null && $student_admission_id != $student->admission_id)
            abort(403);

        if(!Student::where('admission_id', $student_admission_id)->first())
            abort(404);

        $request->validate([
           "from" => "date",
           "to" => "date"
        ]);

        $raw_from_date = $request->input("from");
        $raw_to_date = $request->input("to");

        if($raw_from_date)
            $from_date = date_create($raw_from_date);
        if($raw_to_date)
            $to_date = date_create($raw_to_date);

        $attendance = Attendance::getAttendanceOfStudent(
            $student_admission_id,
            $from_date ?? null,
            $to_date ?? null
        );

        foreach ($attendance as $day){
            $absent = false;
            foreach ($day->absentees as $absentee) {
                if ($student_admission_id == $absentee->student_admission_id)
                    $absent = true;
            }
            $day->absent = $absent;
        }

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
