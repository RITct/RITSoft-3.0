<?php

namespace App\Http\Controllers;

use App\Common\CommonFaculty;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware("permission:faculty.list", ["only" => "index"]);
        $this->middleware("permission:faculty.retrieve", ["only" => "show"]);
        $this->middleware("permission:faculty.create", ["only" => ["create", "store"]]);
        $this->middleware("permission:faculty.update", ["only" => ["edit", "update"]]);
        $this->middleware("permission:faculty.delete", ["only" => "destroy"]);
    }

    public function index(Request $request)
    {
        // Only HOD, Principal
        $faculties = Faculty::with("courses", "advisorClassroom");
        $auth_user = Auth::user();

        // Explicitly given, cause technically an HOD, Dean, Principal can be the same person
        // Although highly unlikely
        if ($auth_user->isAdmin() || $auth_user->faculty->isPrincipal()) {
        } elseif ($auth_user->faculty->isHod()) {
            $faculties = $faculties->where("department_code", $auth_user->faculty->department_code);
        }
        $department_code = $request->input("department_code");
        return view(
            "faculty.index",
            ["data" => CommonFaculty::serializeFacultyByDepartment($faculties->get(), $department_code)]
        );
    }

    public function create()
    {
        return view("attendance.create");
    }

    public function store(FacultyRequest $request)
    {

    }
}
