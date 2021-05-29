<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Attendance;
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

    public function show($student_admission_id){
        // Here student admission id makes more sense than attendance id
        // Same as index
        // Student can view their only
        $student = Auth::user()->student;

        if($student == null || $student_admission_id != $student->admission_id)
            abort(403);
        return view("attendance.retrieve");
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
