<?php

namespace App\Http\Controllers;

use App\Common\CommonFaculty;
use App\Http\Requests\FacultyRequest;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
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

        $this->middleware("same_faculty", ["only" => ["edit", "update"]]);
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
            ["data" => CommonFaculty::serializeFacultyByDepartment($faculties->get(), $auth_user, $department_code)]
        );
    }

    public function create()
    {
        return view("faculty.create");
    }

    public function store(FacultyRequest $request)
    {
        // TODO Send email to faculty with a random password after integrating mailing system
        $auth_user = Auth::user();
        $user = User::create([
            "username" => $request->input("id"),
            "password" => $request->input("id"),
            "email" => $request->input("email"),
        ]);
        $faculty = new Faculty([
            "name" => $request->input("name"),
            "phone" => $request->input("phone"),
        ]);
        $faculty->user_id = $user->id;
        $faculty->id = $request->id;
        if ($auth_user->isAdmin()) {
            $department_code = $request->input("department_code");
            if (!$department_code) {
                return back()->withErrors("department_code is required");
            }
            $faculty->department_code = $department_code;
        } else {
            $faculty->department_code = $auth_user->faculty->department_code;
        }
        $faculty->save();

        $user->faculty()->associate($faculty);

        return redirect("/faculty");
    }

    public function show($faculty_id)
    {
        $auth_user = Auth::user();
        $faculty = Faculty::with("user")->findOrFail($faculty_id);

        $has_unrestricted_access = $auth_user->isAdmin() || $auth_user->faculty->isPrincipal();
        $is_department_hod = $auth_user->faculty?->isHOD()
            && $faculty->department_code == $auth_user->faculty->department_code;
        $is_same_faculty = $faculty_id == $auth_user->faculty_id;

        if ($has_unrestricted_access || $is_department_hod || $is_same_faculty) {
            return view("faculty.retrieve", ["faculty" => CommonFaculty::markEditableFaculty($faculty, $auth_user)]);
        }
        abort(403);
    }

    public function edit($faculty_id)
    {
        $faculty = Faculty::with("user")->findOrFail($faculty_id);
        return view("faculty.edit", ["faculty" => $faculty]);
    }

    public function update(FacultyRequest $request, $faculty_id)
    {
        $faculty = Faculty::with("user")->findOrFail($faculty_id);
        $faculty_update_array = [
            "name" => $request->json("name"),
            "phone" => $request->json("phone")
        ];
        $faculty->update(array_filter($faculty_update_array));

        if ($request->json("email")) {
            $faculty->user->update(["email" => $request->json("email")]);
        }

        return response("OK");
    }

    public function destroy($faculty_id)
    {
        $auth_user = Auth::user();
        $faculty = Faculty::findOrFail($faculty_id);
        $isHODSameDepartment = $faculty->department_code == $auth_user->faculty?->department_code;
        $sameFaculty = $auth_user->faculty_id == $faculty_id;
        if ($auth_user->faculty?->isHOD() && $isHODSameDepartment && !$sameFaculty || $auth_user->isAdmin()) {
            Faculty::destroy(array($faculty_id));
            return response("OK");
        } elseif ($auth_user->faculty_id == $faculty_id) {
            abort(400, "You can't delete yourself");
        }
        abort(403);
    }
}
