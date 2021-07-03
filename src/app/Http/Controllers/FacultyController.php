<?php

namespace App\Http\Controllers;

use App\Exceptions\IntendedException;
use App\Http\Requests\FacultyRequest;
use App\Models\Faculty;
use App\Services\FacultyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    protected FacultyService $service;

    public function __construct(FacultyService $facultyService)
    {
        $this->middleware("permission:faculty.list", ["only" => "index"]);
        $this->middleware("permission:faculty.retrieve", ["only" => "show"]);
        $this->middleware("permission:faculty.create", ["only" => ["create", "store"]]);
        $this->middleware("permission:faculty.update", ["only" => ["edit", "update"]]);
        $this->middleware("permission:faculty.delete", ["only" => "destroy"]);

        $this->service = $facultyService;
    }

    public function index(Request $request)
    {
        // Only HOD, Principal
        $authUser = Auth::user();

        $faculties = $this->service->getFacultiesAccordingToAuthUser($authUser);
        $data = $this->service->serializeFacultyByDepartment($faculties, $authUser, $request->input("department_code"));
        return view("faculty.index", ["data" => $data]);
    }

    public function create()
    {
        return view("faculty.create");
    }

    public function store(FacultyRequest $request)
    {
        // TODO Send email to faculty with a random password after integrating mailing system
        $authUser = Auth::user();
        try {
            $this->service->createFaculty($request->input(), $authUser);
        } catch (IntendedException $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect(route("faculty.index"));
    }

    public function show(FacultyRequest $request)
    {
        return view(
            "faculty.retrieve",
            ["faculty" => $this->service->markEditableFaculty($request->faculty, $request->authUser)]
        );
    }

    public function edit(FacultyRequest $request)
    {
        return view("faculty.edit", ["faculty" => $request->faculty]);
    }

    public function update(FacultyRequest $request)
    {
        $this->service->updateFaculty(
            faculty: $request->faculty,
            name: $request->json("name"),
            phone: $request->json("phone"),
            email: $request->json("email")
        );

        return response("OK");
    }

    public function destroy(FacultyRequest $_, $facultyId)
    {
        Faculty::destroy(array($facultyId));
        return response("OK");
    }
}
